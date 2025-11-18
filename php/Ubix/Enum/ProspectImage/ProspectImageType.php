<?php

declare(strict_types=1);

namespace Ubix\Enum\ProspectImage;

/**
 * Enumeration of prospect image type
 *
 * @see \Ubix\Tests\Enum\ProspectImage\ProspectImageTypeTest PHPUnit test case
 */
enum ProspectImageType: string
{
    case PHOTO_ID_FRONT = 'photo_id_front';
    case PHOTO_ID       = 'photo_id';
    case PHOTO_ID_BACK  = 'photo_id_back';
    case PHOTO_ID_FACE  = 'photo_id_face';
    case IMAGES         = 'images';
}
