<?php

return [

    /*
     * Authentication
     */
	'email' => env('NS_EMAIL'),
	'password' => env('NS_PASSWORD'),

    /*
     * Search options
     */
	'card_number' => env('NS_CARD_NUMBER'),

    /*
     * Personal information for filling in the money back form
     */
    'bank_account_number' => env('BANK_ACCOUNT_NUMBER'),
    'bank_account_holder' => env('BANK_ACCOUNT_HOLDER'),
    'bank_account_holder_city' => env('BANK_ACCOUNT_HOLDER_CITY')
];