<?php

// config for Thuleen/Ssomfa
return [
    'api_url' => env('SSOMFA_API_URL', 'https://ehrdev.moha.gov.my/api'),
    'smart-contract' => [
        'contract_json' => base_path('vendor/thuleen/ssomfa/config/smart-contracts/contracts/Mfa.sol/Mfa.json'),
    ],
];
