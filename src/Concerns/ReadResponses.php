<?php

namespace Blashbrook\PAPIClient\Concerns;

trait ReadResponses
{
    /**
     * @throws \JsonException
     */
    private function toArray($response)
    {
        return json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }
}
