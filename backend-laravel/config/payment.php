<?php

return [
    'cash' => [
        'meeting_label' => env('PAYMENT_CASH_MEETING_LABEL', 'Rendez-vous de paiement avant remise des cles'),
        'meeting_location' => env('PAYMENT_CASH_MEETING_LOCATION', 'Point de remise des cles DarNa'),
        'meeting_note' => env('PAYMENT_CASH_MEETING_NOTE', 'Le client choisit un rendez-vous pour regler l acompte en especes avant de recuperer les cles.'),
    ],
    'bank_transfer' => [
        'beneficiary_name' => env('PAYMENT_BANK_BENEFICIARY_NAME', 'DarNa Operations'),
        'bank_name' => env('PAYMENT_BANK_NAME', 'Banque a configurer'),
        'account_number' => env('PAYMENT_BANK_ACCOUNT_NUMBER', 'A configurer'),
        'iban' => env('PAYMENT_BANK_IBAN', 'A configurer'),
        'swift' => env('PAYMENT_BANK_SWIFT', 'A configurer'),
        'note' => env('PAYMENT_BANK_NOTE', 'Le client effectue le virement de l acompte sur ce compte en indiquant la reference de reservation.'),
    ],
];
