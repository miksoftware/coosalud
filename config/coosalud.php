<?php

return [
    'base_url' => env('COOSALUD_API_URL', 'https://puntofacilapi.coosalud.com/puntofacilback/api'),
    'affiliate_endpoint' => '/Affiliate/afiliateByDoc',
    'document_type' => '1', // Cédula de ciudadanía
    'timeout' => 30,
    'delay' => 500, // ms entre peticiones para no saturar
    'retry_times' => 2,
    'retry_sleep' => 1000,
];
