<?php

use Dotenv\Dotenv;

$packageRoot = dirname(__DIR__, 1);
$dotenv = Dotenv::createImmutable($packageRoot);
$dotenv->load();

// config for Thuleen/Ssomfa
return [
    'api_url' => env('SSOMFA_API_URL', 'https://88d9-2001-f40-906-2771-5d54-9efa-5639-7934.ngrok-free.app'),
    'smart-contract' => [
        'contract_json' => base_path('vendor/thuleen/ssomfa/config/smart-contracts/contracts/Mfa.sol/Mfa.json'),
    ],
];
