<?php

namespace Blashbrook\PAPIClient\Services;

use Blashbrook\PAPIClient\Concerns\FetchData;
use Blashbrook\PAPIClient\Models\PatronCode;
use Blashbrook\PAPIClient\PAPIClient;

class PatronCodeService
{
    use FetchData;

    protected PAPIClient $papiclient;

    public function __construct(PAPIClient $papiclient)
    {
        $this->papiclient = $papiclient;
    }

    public function fetch(): int
    {
        $patronCodes = $this->fetchData('patroncodes', 'PatronCodesRows');
        $patronCodeIds = [];

        foreach ($patronCodes as $patronCode) {
            PatronCode::updateOrCreate(
                ['PatronCodeID' => $patronCode['PatronCodeID']],
                ['Description' => $patronCode['Description']]
            );
            $patronCodeIds[] = $patronCode['PatronCodeID'];
        }

        // Delete local records not in the API
        PatronCode::whereNotIn('PatronCodeID', $patronCodeIds)->delete();

        return count($patronCodeIds);
    }
}
