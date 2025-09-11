<?php

namespace Blashbrook\PAPIClient\Concerns;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;

trait GetConfig
{
    /**
     * Returns LogonWorkstationID from PAPI_LOGONWORKSTATIONID if it is not supplied.
     *
     * @param  null  $logonWorkstationID  - Polaris WorkstationID
     * @return Repository|Application|mixed
     */
    protected static function setLogonWorkstationID($logonWorkstationID = null): mixed
    {
        return ($logonWorkstationID) ? $logonWorkstationID : config('papiclient.logonWorkstationID');
    }

    /**
     * Returns LogonBranchID from PAPI_LOGONBRANCHID if it is not supplied.
     *
     * @param  null  $patronBranchID
     * @return Repository|Application|mixed
     */
    protected static function setPatronBranchID($patronBranchID = null): mixed
    {
        return ($patronBranchID) ? $patronBranchID : config('papiclient.logonBranchID');
    }

    /**
     * Adds Library-specific parameters required to make a Polaris API request.
     *
     * The function setPatronBranchID accepts a specified BranchID for Polaris,
     * or defaults to the LogonBranchID environment variable.
     *
     * The function setLoginWorkstationID accepts a specified WorkstationID for Polaris,
     * or defaults to the LoginWorkstationID environment variable.
     *
     * @param  $params
     * @return array
     */
    protected static function getPolarisSettings($params): array
    {
        $params = Arr::prepend($params, self::setPatronBranchID(), 'PatronBranchID');

        return  Arr::prepend($params, self::setLogonWorkstationID(), 'LogonWorkstationID');
    }
}
