<?php

namespace App\Helpers;

class PptConfigGenerator
{
    public static function addNetCostingConfig($config)
    {
        array_push($config, 'with_net_costing');

        return array_unique($config);
    }

    public static function hasNetCostingConfig($config)
    {
        if (! $config) return false;

        return in_array('with_net_costing', $config);
    }
}