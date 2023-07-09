<?php

namespace App\Enums;

class MediaCollectionType extends AbstractEnum
{
    const UNASSIGNED         = 'unassigned';
    const REPORT_ATTACHMENTS = 'report_collection';
    const ITEM_IMAGE         = 'item_image';
    const USER_AVATAR        = 'user_avatar';
    const COUNTRY_FLAG_IMAGE = 'country_flag_image';
    const COUNTRY_BACKGROUND = 'country_background';
}
