<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Office IP Addresses
    |--------------------------------------------------------------------------
    |
    | Comma-separated values via env, e.g.:
    | OFFICE_IPS=103.101.212.168,127.0.0.1,::1
    |
    */
    'office_ips' => array_values(array_filter(array_map(
        static fn($ip) => trim((string) $ip),
        explode(',', (string) env('OFFICE_IPS', '103.101.212.168,::1'))
    ))),
];

