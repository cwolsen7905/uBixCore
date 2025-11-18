<?php

declare(strict_types=1);

namespace Ubix\Service;

use Imagick;
use ImagickException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\ImageCropInstructions;
use Ubix\Enum\Exception\ExceptionCode;

/**
 * Service to manage image processing
 *
 * @see \Ubix\Tests\Service\ImageServiceTest PHPUnit test case
 */
final class ImageService
{
    /**
     * Constructor
     *
     * @param Logger $logger Logger
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
    ) {
    }

    /**
     * Crop an image
     *
     * @param string                $imageBinary  The image binary
     * @param ImageCropInstructions $instructions The image crop instructions
     *
     * @throws InvalidArgumentException If the image could not be processed
     *
     * @return string The cropped image binary
     */
    public function cropImage(
        string $imageBinary,
        ImageCropInstructions $instructions,
    ): string {
        $imagick = new Imagick();

        try {
            $imagick->readImageBlob($imageBinary);

            if ($instructions->rotate !== 0) {
                $imagick->rotateImage('#ffffff', $instructions->rotate);
            }

            $imagick->cropImage($instructions->width, $instructions->height, $instructions->x, $instructions->y);

            $croppedBinary = $imagick->getImagesBlob();
        } catch (ImagickException $e) {
            throw new InvalidArgumentException(
                'Failed to process image: ' . $e->getMessage(),
                ExceptionCode::PROSPECT_APPLICATION_IMAGE_INVALID->value,
            );
        } finally {
            $imagick->clear();
            $imagick->destroy();
        }

        return $croppedBinary;
    }
}
