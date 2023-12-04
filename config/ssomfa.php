<?php

use Dotenv\Dotenv;

$packageRoot = dirname(__DIR__, 1);
$dotenv = Dotenv::createImmutable($packageRoot);
$dotenv->load();

// config for Thuleen/Ssomfa
// https://ehrdev.moha.gov.my/api
return [
    'api_url' => env('SSOMFA_API_URL', 'https://ehrdev.moha.gov.my/api'),
    'smart-contract' => [
        'contract_json' => base_path('vendor/thuleen/ssomfa/config/smart-contracts/contracts/Mfa.sol/Mfa.json'),
    ],
];
