<?php

declare(strict_types=1);

namespace Ubix\Enum\Creator;

/**
 * The starter category taxonomy for creators (creator-profile SRS Q3, adopted as built)
 *
 * @see \Ubix\Tests\Enum\Creator\CreatorCategoryTest PHPUnit test case
 */
enum CreatorCategory: string
{
    case PASTOR    = 'pastor';
    case WORSHIP   = 'worship';
    case TEACHER   = 'teacher';
    case PODCASTER = 'podcaster';
    case AUTHOR    = 'author';
    case ARTIST    = 'artist';
    case OTHER     = 'other';
}
