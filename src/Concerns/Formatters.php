<?php

namespace Blashbrook\PAPIClient\Concerns;

use Carbon\Carbon;

trait Formatters
{
    /**
     * Formats timestamp in milliseconds to YYYY-MM-DD.
     *
     * @param  $timestamp
     * @return string
     */
    private function formatToDateString($timestamp): string
    {
        return Carbon::createFromTimestampMs($timestamp)->toDateString();
    }
}
