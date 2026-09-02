<?php

declare(strict_types=1);

namespace Ubix\Controller\SowingMeApi;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Controller\AbstractController as Controller;
use Ubix\Enum\StatusCode;
use Ubix\Enum\Tier\TierStatus;
use Ubix\Exception\DtoException;
use Ubix\Model\Tier;
use Ubix\Payload\Request\TierReorderRequestPayload;
use Ubix\Payload\Request\TierRequestPayload;
use Ubix\Renderer\TemplateRenderer;
use Ubix\Service\CreatorProfileService;
use Ubix\Service\JsonService;
use Ubix\Service\TierService;

/**
 * Creator tier management + public tier listing (subscription-tiers TDS §3)
 *
 * @see \Ubix\Tests\Controller\SowingMeApi\TierControllerTest PHPUnit test case
 */
final class TierController extends Controller
{
    /**
     * Constructor
     *
     * @param Logger                $logger                The Monolog logger
     * @param TemplateRenderer      $view                  The template renderer
     * @param JsonService           $jsonService           The JSON service
     * @param CreatorProfileService $creatorProfileService Resolves the session user's creator row / public slugs
     * @param TierService           $tierService           The tier service
     */
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view,
        protected JsonService $jsonService,
        protected CreatorProfileService $creatorProfileService,
        protected TierService $tierService,
    ) {
        parent::__construct($logger, $view, $jsonService);
    }

    /**
     * POST /creator/tiers — create a tier (FR-101)
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function create(Request $request, Response $response): Response
    {
        $creatorId = $this->resolveOwnCreatorId();
        if ($creatorId === null) {
            return $this->renderJson($response, ['message' => 'Creator profile required'], StatusCode::FORBIDDEN);
        }

        try {
            $payload = TierRequestPayload::getRequest($request);
            assert($payload instanceof TierRequestPayload);
        } catch (DtoException $e) {
            return $this->renderJson($response, [
                'fields'     => $e->getDto()->errors ?? [],
                'message'    => $e->getMessage(),
                'statusCode' => $e->getCode(),
            ], StatusCode::BAD_REQUEST);
        }

        $tier = new Tier(
            creatorId:       $creatorId,
            name:            $payload->name,
            description:     $payload->description,
            priceAmount:     $payload->priceAmount->value,
            priceCurrency:   $payload->priceCurrency ?? 'USD',
            billingInterval: $payload->billingInterval,
            status:          TierStatus::ACTIVE,
        );

        $benefits = $this->stringList($payload->benefits ?? []);
        $this->tierService->createTier($tier, $benefits);

        return $this->renderJson($response, [
            'status' => 'success',
            'tier'   => ['id' => $tier->getId(), 'position' => $tier->getPosition()],
        ], StatusCode::CREATED);
    }

    /**
     * GET /creator/tiers — list own tiers including archived
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function listOwn(Request $request, Response $response): Response // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- Slim route signature
    {
        $creatorId = $this->resolveOwnCreatorId();
        if ($creatorId === null) {
            return $this->renderJson($response, ['message' => 'Creator profile required'], StatusCode::FORBIDDEN);
        }

        return $this->renderJson($response, [
            'status' => 'success',
            'tiers'  => $this->composeTiers($this->tierService->listTiers($creatorId)),
        ]);
    }

    /**
     * PATCH /creator/tiers/{id} — edit name/description/price/interval (FR-101)
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     * @param string   $id       The tier id from the route
     *
     * @return Response The modified response object with the operation result.
     */
    public function update(Request $request, Response $response, string $id): Response
    {
        $creatorId = $this->resolveOwnCreatorId();
        if ($creatorId === null) {
            return $this->renderJson($response, ['message' => 'Creator profile required'], StatusCode::FORBIDDEN);
        }

        try {
            $payload = TierRequestPayload::getRequest($request);
            assert($payload instanceof TierRequestPayload);
            $tier = $this->tierService->getOwnTier($creatorId, (int) $id);
        } catch (DtoException $e) {
            return $this->renderJson($response, [
                'fields'     => $e->getDto()->errors ?? [],
                'message'    => $e->getMessage(),
                'statusCode' => $e->getCode(),
            ], StatusCode::BAD_REQUEST);
        } catch (InvalidArgumentException $e) {
            return $this->renderJson($response, ['message' => $e->getMessage()], StatusCode::NOT_FOUND);
        }

        $tier->setName($payload->name);
        $tier->setDescription($payload->description);
        $tier->setPriceAmount($payload->priceAmount->value);
        $tier->setPriceCurrency($payload->priceCurrency ?? 'USD');
        $tier->setBillingInterval($payload->billingInterval);
        $this->tierService->updateTier($tier);

        if ($payload->benefits !== null) {
            $this->tierService->replaceBenefits($tier, $this->stringList($payload->benefits));
        }

        return $this->renderJson($response, ['status' => 'success']);
    }

    /**
     * PATCH /creator/tiers/{id}/status — archive / reactivate (FR-103)
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     * @param string   $id       The tier id from the route
     *
     * @return Response The modified response object with the operation result.
     */
    public function updateStatus(Request $request, Response $response, string $id): Response
    {
        $creatorId = $this->resolveOwnCreatorId();
        if ($creatorId === null) {
            return $this->renderJson($response, ['message' => 'Creator profile required'], StatusCode::FORBIDDEN);
        }

        $body   = (array) $request->getParsedBody();
        $status = TierStatus::tryFrom(is_string($body['status'] ?? null) ? $body['status'] : '');
        if ($status === null) {
            return $this->renderJson($response, ['message' => 'status must be active or archived'], StatusCode::BAD_REQUEST);
        }

        try {
            $tier = $this->tierService->getOwnTier($creatorId, (int) $id);
        } catch (InvalidArgumentException $e) {
            return $this->renderJson($response, ['message' => $e->getMessage()], StatusCode::NOT_FOUND);
        }

        $this->tierService->updateStatus($tier, $status);

        return $this->renderJson($response, ['status' => 'success']);
    }

    /**
     * POST /creator/tiers/reorder — transactional renumbering (FR-105)
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     *
     * @return Response The modified response object with the operation result.
     */
    public function reorder(Request $request, Response $response): Response
    {
        $creatorId = $this->resolveOwnCreatorId();
        if ($creatorId === null) {
            return $this->renderJson($response, ['message' => 'Creator profile required'], StatusCode::FORBIDDEN);
        }

        try {
            $payload = TierReorderRequestPayload::getRequest($request);
            assert($payload instanceof TierReorderRequestPayload);
            $tierIds = [];
            foreach ($payload->tierIds as $tierId) {
                if (is_int($tierId) || (is_string($tierId) && ctype_digit($tierId))) {
                    $tierIds[] = (int) $tierId;
                }
            }
            $this->tierService->reorderTiers($creatorId, $tierIds);
        } catch (DtoException $e) {
            return $this->renderJson($response, [
                'fields'     => $e->getDto()->errors ?? [],
                'message'    => $e->getMessage(),
                'statusCode' => $e->getCode(),
            ], StatusCode::BAD_REQUEST);
        } catch (InvalidArgumentException $e) {
            return $this->renderJson($response, ['message' => $e->getMessage()], StatusCode::BAD_REQUEST);
        }

        return $this->renderJson($response, ['status' => 'success']);
    }

    /**
     * GET /creators/{slug}/tiers — public active tiers + synthetic free entry (FR-102)
     *
     * @param Request  $request  The HTTP request object containing client data.
     * @param Response $response The HTTP response object used to send data back to the client
     * @param string   $slug     The creator slug from the route
     *
     * @return Response The modified response object with the operation result.
     */
    public function publicList(Request $request, Response $response, string $slug): Response // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- Slim route signature
    {
        $creator   = $this->creatorProfileService->getCreatorBySlug($slug);
        $creatorId = $creator?->getId();
        if ($creatorId === null) {
            return $this->renderJson($response, ['message' => 'Creator not found'], StatusCode::NOT_FOUND);
        }

        $tiers = $this->composeTiers($this->tierService->listTiers($creatorId, true));
        array_unshift($tiers, ['position' => 0, 'name' => 'Free', 'priceAmount' => 0, 'benefits' => []]);

        return $this->renderJson($response, ['status' => 'success', 'tiers' => $tiers]);
    }

    /**
     * The session user's creator id, or null when they have no creator row
     *
     * @return ?int The creator id
     */
    private function resolveOwnCreatorId(): ?int
    {
        $sessionUser = $_SESSION['user'] ?? null;
        $userId      = is_array($sessionUser) ? ($sessionUser['id'] ?? null) : null;
        if (!is_int($userId) && !is_string($userId)) {
            return null;
        }

        return $this->creatorProfileService->getCreatorByUserId((int) $userId)?->getId();
    }

    /**
     * Compose the wire shape for a list of tiers, with their benefits attached
     *
     * @param array<int, Tier> $tiers The tiers
     *
     * @return array<int, array<string, mixed>> The wire rows
     */
    private function composeTiers(array $tiers): array
    {
        $tierIds = [];
        foreach ($tiers as $tier) {
            if ($tier->getId() !== null) {
                $tierIds[] = $tier->getId();
            }
        }

        $benefitsByTier = [];
        foreach ($this->tierService->listBenefits($tierIds) as $benefit) {
            $benefitTierId = $benefit->getTierId();
            if ($benefitTierId !== null) {
                $benefitsByTier[$benefitTierId][] = $benefit->getDescription();
            }
        }

        $rows = [];
        foreach ($tiers as $tier) {
            $rows[] = [
                'benefits'        => $tier->getId() !== null ? ($benefitsByTier[$tier->getId()] ?? []) : [],
                'billingInterval' => $tier->getBillingInterval()?->value,
                'description'     => $tier->getDescription(),
                'id'              => $tier->getId(),
                'name'            => $tier->getName(),
                'position'        => $tier->getPosition(),
                'priceAmount'     => $tier->getPriceAmount(),
                'priceCurrency'   => $tier->getPriceCurrency(),
                'status'          => $tier->getStatus()?->value,
            ];
        }

        return $rows;
    }

    /**
     * Normalise a mixed array into a clean list of non-empty strings
     *
     * @param array<int|string, mixed> $values The raw values
     *
     * @return array<int, string> The string list
     */
    private function stringList(array $values): array
    {
        $strings = [];
        foreach ($values as $value) {
            if (is_string($value) && trim($value) !== '') {
                $strings[] = trim($value);
            }
        }

        return $strings;
    }
}
