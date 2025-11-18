<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for image crop instructions
 *
 * @see \Ubix\Tests\DataTransferObject\ImageCropInstructionsTest PHPUnit test case
 */
final readonly class ImageCropInstructions implements Dto
{
    /**
     * Constructor
     *
     * @param int $x      The X coordinate
     * @param int $y      The Y coordinate
     * @param int $width  The width
     * @param int $height The height
     * @param int $rotate The rotation angle
     */
    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly int $width,
        public readonly int $height,
        public readonly int $rotate,
    ) {
    }
}
