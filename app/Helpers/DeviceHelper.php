<?php

use Jenssegers\Agent\Agent;

if (!function_exists('isMobile')) {
    /**
     * Check if the current request is from a mobile device.
     *
     * @return bool
     */
    function isMobile(): bool
    {
        $agent = new Agent();
        return $agent->isMobile();
    }
}
