<?php

namespace SergiX44\Nutgram\Telegram\Types;

/**
 * This object represent a user's profile pictures.
 * @see https://core.telegram.org/bots/api#userprofilephotos
 */
class UserProfilePhotos
{
    /**
     * Total number of profile pictures the target user has
     * @var int
     */
    public int $total_count;

    /**
     * Requested profile pictures (in up to 4 sizes each)
     * @var PhotoSize[][]
     */
    public array $photos;
}
