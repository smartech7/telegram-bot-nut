<?php

namespace SergiX44\Nutgram\Telegram\Types\Passport;

use SergiX44\Nutgram\Telegram\Enums\PassportSource;
use SergiX44\Nutgram\Telegram\Enums\PassportType;

/**
 * Represents an issue with a list of scans. The error is considered resolved when the list of files containing the
 * scans changes.
 * @see https://core.telegram.org/bots/api#passportelementerrorfiles
 */
class PassportElementErrorFiles extends PassportElementError
{
    /**
     * Error source, must be files
     */
    public PassportSource $source = PassportSource::FILES;

    /**
     * The section of the user's Telegram Passport which has the issue, one of “utility_bill”,
     * “bank_statement”, “rental_agreement”, “passport_registration”, “temporary_registration”
     */
    public PassportType $type;

    /**
     * Base64-encoded file hashes
     */
    public string $file_hash;

    /**
     * Error message
     */
    public string $message;
}
