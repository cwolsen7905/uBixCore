<?php

declare(strict_types=1);

namespace Ubix\Service;

use DateTime;
use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Model\FanClub;
use Ubix\Model\FanClubComment;
use Ubix\Model\FanClubLike;
use Ubix\Model\FanClubMembership;
use Ubix\Model\FanClubPost;
use Ubix\Model\FanClubPostUnlock;
use Ubix\Model\FanClubTransaction;
use Ubix\Model\PlatformUser;
use Ubix\Repository\FanClub\FanClubReaderInterface as FanClubReader;
use Ubix\Repository\FanClub\FanClubWriterInterface as FanClubWriter;
use Ubix\Repository\FanClubComment\FanClubCommentReaderInterface as FanClubCommentReader;
use Ubix\Repository\FanClubComment\FanClubCommentWriterInterface as FanClubCommentWriter;
use Ubix\Repository\FanClubLike\FanClubLikeReaderInterface as FanClubLikeReader;
use Ubix\Repository\FanClubLike\FanClubLikeWriterInterface as FanClubLikeWriter;
use Ubix\Repository\FanClubMembership\FanClubMembershipReaderInterface as FanClubMembershipReader;
use Ubix\Repository\FanClubMembership\FanClubMembershipWriterInterface as FanClubMembershipWriter;
use Ubix\Repository\FanClubPost\FanClubPostReaderInterface as FanClubPostReader;
use Ubix\Repository\FanClubPostUnlock\FanClubPostUnlockWriterInterface as FanClubPostUnlockWriter;
use Ubix\Repository\FanClubTransaction\FanClubTransactionReaderInterface as FanClubTransactionReader;
use Ubix\Repository\PlatformUser\PlatformUserReaderInterface as PlatformUserReader;

/**
 * Service to interact with fan clubs
 *
 * @see \Ubix\Tests\Service\FanClubServiceTest PHPUnit test case
 */
final class FanClubService
{
    public const TRENDING_POSTS_COUNT = 3;

    /**
     * Constructor
     *
     * @param Logger                   $logger                   Logger
     * @param FanClubCommentReader     $fanClubCommentReader     Fan club comment reader
     * @param FanClubCommentWriter     $fanClubCommentWriter     Fan club comment writer
     * @param FanClubLikeReader        $fanClubLikeReader        Fan club like reader
     * @param FanClubLikeWriter        $fanClubLikeWriter        Fan club like writer
     * @param FanClubMembershipReader  $fanClubMembershipReader  Fan club membership reader
     * @param FanClubMembershipWriter  $fanClubMembershipWriter  Fan club membership writer
     * @param FanClubPostReader        $fanClubPostReader        Fan club post reader
     * @param FanClubPostUnlockWriter  $fanClubPostUnlockWriter  Fan club post unlock writer
     * @param FanClubReader            $fanClubReader            Fan club reader
     * @param FanClubTransactionReader $fanClubTransactionReader Fan club transaction reader
     * @param FanClubWriter            $fanClubWriter            Fan club writer
     * @param PlatformUserReader       $platformUserReader       Platform user reader
     * @param TransactionService       $transactionService       Transaction service
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private FanClubCommentReader $fanClubCommentReader,
        private FanClubCommentWriter $fanClubCommentWriter,
        private FanClubLikeReader $fanClubLikeReader,
        private FanClubLikeWriter $fanClubLikeWriter,
        private FanClubMembershipReader $fanClubMembershipReader,
        private FanClubMembershipWriter $fanClubMembershipWriter,
        private FanClubPostReader $fanClubPostReader,
        private FanClubPostUnlockWriter $fanClubPostUnlockWriter,
        private FanClubReader $fanClubReader,
        private FanClubTransactionReader $fanClubTransactionReader,
        private FanClubWriter $fanClubWriter,
        private PlatformUserReader $platformUserReader,
        private TransactionService $transactionService,
    ) {
    }

    /**
     * Get a fan club by performer ID
     *
     * @param int $performerId The performer's ID
     *
     * @throws Exception If a fan club isn't found
     *
     * @return FanClub The fan club belonging to the performer passed
     */
    public function getByPerformerId(int $performerId): FanClub
    {
        $fanClubs = $this->fanClubReader->get($performerId);
        if (count($fanClubs) > 0) {
            return $fanClubs[0];
        } else {
            throw new Exception('No fan club found with the model ID `' . $performerId . '`', ExceptionCode::NO_MATCHES_FOUND_FOR_FAN_CLUB_BY_PERFORMER_ID->value);
        }
    }

    /**
     * Change the price on a fan club
     *
     * @param FanClub $fanClub The fan club to change
     * @param int     $credits The price to change to
     *
     * @throws InvalidArgumentException If the credits are an invalid amount
     *
     * @return void
     */
    public function changePrice(FanClub $fanClub, int $credits): void
    {
        //
        //  Validate parameters
        //
        if ($credits < 1 || $credits > FanClub::MONTHLY_PRICE_MAXIMUM) {
            throw new InvalidArgumentException('The monthly price of the fan club must be between 1 and ' . FanClub::MONTHLY_PRICE_MAXIMUM . '.', ExceptionCode::MONTHLY_PRICE_OUT_OF_RANGE->value);
        }

        //
        //  Update the price
        //
        $fanClub->setMonthlyPrice($credits);
        $fanClub->setMonthlyPriceUpdatedAt(new DateTime());

        $this->fanClubWriter->save($fanClub);
    }

    /**
     * Gift a membership to a fan club
     *
     * @param FanClub $fanClub    The fan club to gift a membership to
     * @param string  $screenName The screen name of the platform user to receive the gift
     * @param int     $days       The number of days the gifted membership will be active for
     *
     * @throws InvalidArgumentException If an invalid parameter value was passed
     *
     * @return void
     */
    public function giftMembership(FanClub $fanClub, string $screenName, int $days): void
    {
        //
        //  Validate parameters
        //
        if (trim($screenName) === '') {
            throw new InvalidArgumentException('You must enter a screen name.', ExceptionCode::MISSING_SCREEN_NAME->value);
        }

        if ($days < 1 || $days > FanClub::DAYS_FOR_GIFT_MAXIMUM) {
            throw new InvalidArgumentException('The duration of the gifted membership must be between 1 and ' . FanClub::DAYS_FOR_GIFT_MAXIMUM . '.', ExceptionCode::GIFTED_DAYS_OUT_OF_RANGE->value);
        }

        //
        //  Identify the platform user by screen name
        //
        $gifted = $this->platformUserReader->getByScreenName($screenName);
        if (count($gifted) === 0) {
            throw new InvalidArgumentException('The screen name `' . $screenName . '` was not found.', ExceptionCode::SCREEN_NAME_NOT_FOUND_FOR_GIFT->value);
        }
        $gifted = $gifted[0];

        /*
            // NOT_IMPLEMENTED: should we do something if there is already an active membership for the $gifted platform user on the fan club?
        */

        //
        //  Create a new transaction object and send it to the transaction service to be saved
        //
        $transaction = new FanClubTransaction(
            userId:        $gifted->getUserId(),
            modelId:       $fanClub->getModelId(),
            type:          'membership',
            amountCredits: 0,
            screenName:    $screenName,
            broadcasterId: $fanClub->getPerformer()?->getBroadcasterId(),
            studioCode:    $fanClub->getPerformer()?->getStudioCode(),
            locationId:    0, // NOT_IMPLEMENTED: is this right? I am seeing 0 and 28049 in the dev database
            createdAt:     new DateTime(),
        );

        $this->transactionService->newTransaction($gifted, $transaction);

        //
        //  Create a new membership object
        //
        $membership = new FanClubMembership(
            userId:               $gifted->getUserId(),
            modelId:              $fanClub->getModelId(),
            membershipPriceId:    0,
            startDate:            new DateTime(),
            endDate:              (new DateTime())->modify('+' . $days . ' days')->setTime(23, 59, 59),
            status:               'active',
            membershipType:       'gifted',
            screenName:           $screenName,
            transactionId:        $transaction->getId(),
            refFanclubTransactId: 0,
            createdAt:            new DateTime(),
        );

        $this->fanClubMembershipWriter->save($membership);
    }

    /**
     * Make a platform user join a fan club
     *
     * @param FanClub      $fanClub      The fan club to join
     * @param PlatformUser $platformUser The platform user joining the fan club
     *
     * @return void
     */
    public function join(FanClub $fanClub, PlatformUser $platformUser): void
    {
        // NOT_IMPLEMENTED: check for existing membership with the logged_in_account to prevent double memberships?
        //
        //  Create a new transaction object and send it to the transaction service to be saved
        //
        $transaction = new FanClubTransaction(
            userId:        $platformUser->getUserId(),
            modelId:       $fanClub->getModelId(),
            type:          'membership',
            amountCredits: $fanClub->getMonthlyPrice(),
            screenName:    $platformUser->getScreenName(),
            broadcasterId: $fanClub->getPerformer()?->getBroadcasterId(),
            studioCode:    $fanClub->getPerformer()?->getStudioCode(),
            locationId:    0, // NOT_IMPLEMENTED: is this right? I am seeing 0 and 28049 in the dev database
            createdAt:     new DateTime(),
        );

        $this->transactionService->newTransaction($platformUser, $transaction);

        //
        //  Create a new membership object
        //
        $membership = new FanClubMembership(
            userId:               $platformUser->getUserId(),
            modelId:              $fanClub->getModelId(),
            membershipPriceId:    0,
            startDate:            new DateTime(),
            endDate:              (new DateTime())->modify('+30 days')->setTime(23, 59, 59), // NOT_IMPLEMENTED: is this right? check Miro's Python code to confirm we are mimicking correctly
            status:               'active',
            membershipType:       'regular',
            screenName:           $platformUser->getScreenName(),
            transactionId:        $transaction->getId(),
            refFanclubTransactId: 0,
            createdAt:            new DateTime(),
        );

        $this->fanClubMembershipWriter->save($membership);
    }

    /**
     * Get the most liked fan club posts for the trending widget
     *
     * @return FanClubPost[] The most liked fan club posts
     */
    public function getMostLikedPosts(): array
    {
        return $this->fanClubPostReader->getByMostLiked();
    }

    /**
     * Get the most commented fan club posts for the trending widget
     *
     * @return FanClubPost[] The most commented fan club posts
     */
    public function getMostCommentedPosts(): array
    {
        return $this->fanClubPostReader->getByMostCommented();
    }

    /**
     * Get fan club posts tagged with the top hashtag for the trending widget
     *
     * @return FanClubPost[] Fan club posts tagged with the top hashtag
     */
    public function getTopHashTagPosts(): array
    {
        return $this->fanClubPostReader->getByTopHashTag();
    }

    /**
     * Get the most recent fan club posts for the trending widget
     *
     * @return FanClubPost[] The most recent fan club posts
     */
    public function getLatestPost(): array
    {
        return $this->fanClubPostReader->getByDateCreated();
    }

    /**
     * Get public fan club posts for the trending widget
     *
     * @return FanClubPost[] Public fan club posts
     */
    public function getPublicPosts(): array
    {
        return $this->fanClubPostReader->getPublicPosts();
    }

    /**
     * Get fan club memberships by performer ID
     *
     * @param int $performerId The performer's ID
     *
     * @return FanClubMembership[] Array of fan club memberships
     */
    public function getMembershipsByPerformerId(int $performerId): array
    {
        return $this->fanClubMembershipReader->getByModelId($performerId);
    }

    /**
     * Get fan club posts by performer ID
     *
     * @param int     $performerId   The performer's ID
     * @param ?string $status        The post's status (optional)
     * @param ?string $visibility    The post's visibility (optional)
     * @param bool    $purchasedOnly The post's purchased status (true = purchased only, false = unpurchased only, null = both) (optional)
     * @param ?int    $userId        The user's ID (optional)
     *
     * @return FanClubPost[] Array of fan club posts
     */
    public function getPostsByPerformerId(int $performerId, ?string $status = 'published', ?string $visibility = null, ?bool $purchasedOnly = null, ?int $userId = null): array
    {
        return $this->fanClubPostReader->getByModelId($performerId, $status, $visibility, $purchasedOnly, $userId);
    }

    /**
     * Get fan club transactions by performer ID
     *
     * @param int $performerId The performer's ID
     *
     * @return FanClubTransaction[] Array of fan club transactions
     */
    public function getTransactionsByPerformerId(int $performerId): array
    {
        return $this->fanClubTransactionReader->getByModelId($performerId);
    }

    /**
     * Get a fan club post by ID
     *
     * @param int $postId The post's ID
     *
     * @throws Exception If a post isn't found
     *
     * @return FanClubPost The post object
     */
    public function getPostById(int $postId): FanClubPost
    {
        $posts = $this->fanClubPostReader->getById($postId);
        if (count($posts) > 0) {
            return $posts[0];
        } else {
            throw new Exception('No fan club post found with the ID `' . $postId . '`', ExceptionCode::NO_MATCHES_FOUND_FOR_FAN_CLUB_POST_BY_POST_ID->value);
        }
    }

    /**
     * Get unlocked fan club posts for by platform user ID
     *
     * @param int  $userId The user's ID
     * @param ?int $postId Individual post ID (optional)
     *
     * @return FanClubPost[] Array of unlocked posts
     */
    public function getUnlockedPostsByUserId(int $userId, ?int $postId = null): array
    {
        return $this->fanClubPostReader->getUnlockedByUserId($userId, $postId);
    }

    /**
     * Get fan club comments for a fan club post
     *
     * @param int $postId The post's ID
     *
     * @return FanClubComment[] Comments for the post
     */
    public function getCommentsByPostId(int $postId): array
    {
        return $this->fanClubCommentReader->getByPostId($postId);
    }

    /**
     * Like a fan club post
     *
     * @param PlatformUser $platformUser The platform user liking the post
     * @param FanClubPost  $post         The post to be liked
     *
     * @throws Exception If the post can't be liked by the user
     *
     * @return void
     */
    public function likePost(PlatformUser $platformUser, FanClubPost $post): void
    {
        assert(is_int($platformUser->getUserId()) && is_int($post->getId()));
        $existing = $this->fanClubLikeReader->get($platformUser->getUserId(), $post->getId());
        if (count($existing) > 0) {
            throw new Exception('You have already liked this post.', ExceptionCode::FAN_CLUB_POST_ALREADY_LIKED->value);
        }

        //
        //  Create a new like object and save it
        //
        $like = new FanClubLike(
            postId:     $post->getId(),
            screenName: $platformUser->getScreenName(),
            userId:     $platformUser->getUserId(),
            createdAt:  new DateTime(),
        );

        $this->fanClubLikeWriter->save($like);
    }

    /**
     * Unlike a fan club post
     *
     * @param PlatformUser $platformUser The platform user unliking the post
     * @param FanClubPost  $post         The post to be unliked
     *
     * @throws Exception If the post can't be unliked by the user
     *
     * @return void
     */
    public function unlikePost(PlatformUser $platformUser, FanClubPost $post): void
    {
        //
        //  Ensure the user has not already unliked the post
        //
        assert(is_int($platformUser->getUserId()) && is_int($post->getId()));
        $existing = $this->fanClubLikeReader->get($platformUser->getUserId(), $post->getId());
        if (count($existing) === 0) {
            throw new Exception('You have already unliked this post.', ExceptionCode::FAN_CLUB_POST_ALREADY_UNLIKED->value);
        }

        //
        //  Delete the existing like
        //
        $this->fanClubLikeWriter->delete($existing[0]);
    }

    /**
     * Comment on a fan club post
     *
     * @param PlatformUser $platformUser The platform user posting the comment
     * @param FanClubPost  $post         The post to be commented on
     * @param string       $text         The comment text to be left
     *
     * @return void
     */
    public function commentOnPost(PlatformUser $platformUser, FanClubPost $post, string $text): void
    {
        //
        //  Create a new comment and save it
        //
        $comment = new FanClubComment(
            commentText: $text,
            postId:      $post->getId(),
            screenName:  $platformUser->getScreenName(),
            status:      'active',
            userId:      $platformUser->getUserId(),
            createdAt:   new DateTime(),
        );

        $this->fanClubCommentWriter->save($comment);
    }

    /**
     * Unlock a fan club post for a platform user
     *
     * @param PlatformUser $platformUser The platform user to receive the unlock
     * @param FanClubPost  $post         The fan club post to unlock
     *
     * @throws Exception If the post unlock fails
     *
     * @return void
     */
    public function unlockPost(PlatformUser $platformUser, FanClubPost $post): void
    {
        //
        //  Check if the performer's fan club is open
        //
        assert(is_int($post->getModelId()));
        $fanClub = $this->getByPerformerId($post->getModelId());
        if ($fanClub->isClosed()) {
            throw new Exception('This post is part of a fan club that has closed.', ExceptionCode::FAN_CLUB_IS_CLOSED->value);
        }

        //
        //  Check if the post can be unlocked
        //
        if ($post->getVisibility() !== 'extra') {
            throw new Exception('This post does not need to be unlocked.', ExceptionCode::FAN_CLUB_POST_NOT_UNLOCKABLE->value);
        }

        //
        //  Check if the user has already unlocked the post
        //
        assert(is_int($platformUser->getUserId()));
        $existing = $this->getUnlockedPostsByUserId($platformUser->getUserId(), $post->getId());
        if (count($existing) > 0) {
            throw new Exception('This post is already unlocked.', ExceptionCode::FAN_CLUB_POST_ALREADY_UNLOCKED->value);
        }

        //
        //  Create a new transaction object and send it to the transaction service to be saved
        //
        $transaction = new FanClubTransaction(
            userId:        $platformUser->getUserId(),
            modelId:       $post->getPerformer()?->getId(),
            type:          'extra',
            amountCredits: $post->getPriceCredits(),
            screenName:    $platformUser->getScreenName(),
            broadcasterId: $post->getPerformer()?->getBroadcasterId(),
            studioCode:    $post->getPerformer()?->getStudioCode(),
            locationId:    0, // NOT_IMPLEMENTED: is this right? I am seeing 0 and 28049 in the dev database
            createdAt:     new DateTime(),
        );

        $this->transactionService->newTransaction($platformUser, $transaction);

        //
        //  Create a new post unlock object
        //
        $postUnlock = new FanClubPostUnlock(
            userId:        $platformUser->getUserId(),
            postId:        $post->getId(),
            screenName:    $platformUser->getScreenName(),
            transactionId: $transaction->getId(),
            contentType:   'other', // NOT_IMPLEMENTED: is this right?
            purchasedAt:   new DateTime(),
        );

        $this->fanClubPostUnlockWriter->save($postUnlock);
    }

    /**
     * Get the active membership by platform user and membership ID
     *
     * @param ?PlatformUser $user         The platform user
     * @param int           $membershipId The membership's ID
     *
     * @throws Exception If the membership isn't found or doesn't belong to the platform user
     *
     * @return FanClubMembership Fan club membership
     */
    public function getMembershipForUserById(?PlatformUser $user, int $membershipId): FanClubMembership
    {
        //
        //  Validate parameters
        //
        if ($user === null) { // NOT_IMPLEMENTED: should I just change the parameter type from "?PlatformUser" to "PlatformUser"? (probably based on this method's name)
            throw new Exception('You do not have permission to access this membership.', ExceptionCode::MEMBERSHIP_FORBIDDEN->value);
        }

        //
        //  Load the membership by ID
        //
        $membership = $this->fanClubMembershipReader->getById($membershipId)[0] ?? null;
        if ($membership === null) {
            throw new Exception('The membership was not found.', ExceptionCode::NO_MATCHES_FOUND_FOR_MEMBERSHIP->value);
        }

        //
        //  Ensure the user has permission to access the membership
        //
        if ($user->getUserId() !== $membership->getUserId()) {
            throw new Exception('You do not have permission to access this membership.', ExceptionCode::MEMBERSHIP_FORBIDDEN->value);
        }

        //
        //  Return the membership
        //
        return $membership;
    }

    /**
     * Get the active membership by platform user ID and performer ID
     *
     * @param int $userId      The user's ID
     * @param int $performerId The performer's ID
     *
     * @return ?FanClubMembership The active membership if one exists otherwise null
     */
    public function getActiveMembershipByUserAndPerformerIds(int $userId, int $performerId): ?FanClubMembership // NOT_IMPLEMENTED: why "active"?
    {
        return $this->fanClubMembershipReader->getActiveByUserAndPerformerIds($userId, $performerId)[0] ?? null;
    }

    /**
     * Reactivate a membership
     *
     * @param FanClubMembership $membership The membership to reactivate
     *
     * @throws Exception If the reactivation fails
     *
     * @return void
     */
    public function reactivateMembership(FanClubMembership $membership): void
    {
        //
        //  Ensure the user can reactivate the membership
        //
        if ($membership->getStatus() !== 'expired' && $membership->getStatus() !== 'canceled') { // NOT_IMPLEMENTED: can we reactivate an expired membership or does a new membership need to be created instead?
            throw new Exception('The membership is not currently active so can not be canceled.', ExceptionCode::MEMBERSHIP_INELIGIBLE_FOR_REACTIVATION->value);
        }

        //
        //  Set the membership's status to active
        //
        $membership->setStatus('active');

        $this->fanClubMembershipWriter->save($membership);
    }

    /**
     * Cancel a membership
     *
     * @param FanClubMembership $membership The membership to cancel
     *
     * @throws Exception If the cancellation fails
     *
     * @return void
     */
    public function cancelMembership(FanClubMembership $membership): void
    {
        //
        //  Ensure the user has not already canceled the membership
        //
        if ($membership->getStatus() !== 'active') {
            throw new Exception('The membership is not currently active so can not be canceled.', ExceptionCode::MEMBERSHIP_ALREADY_CANCELED->value);
        }

        //
        //  Set the membership's status to canceled
        //
        $membership->setStatus('canceled');

        $this->fanClubMembershipWriter->save($membership);
    }

    /**
     * Get fan club memberships by platform user ID
     *
     * @param int $userId The user's ID
     *
     * @return FanClubMembership[] Array of an club memberships
     */
    public function getMembershipsByUserId(int $userId): array
    {
        return $this->fanClubMembershipReader->getByUserId($userId);
    }
}
