<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paramètres de Taxation
    |--------------------------------------------------------------------------
    |
    | Configuration des taxes applicables aux documents (factures, devis).
    |
    */

    'tax' => [
        // Taux de TVA en pourcentage (18% par défaut)
        'rate' => env('TAX_RATE', 18),
        
        // TVA incluse par défaut dans les nouveaux documents
        'included_by_default' => env('TAX_INCLUDED_BY_DEFAULT', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres de Numérotation
    |--------------------------------------------------------------------------
    |
    | Préfixes utilisés pour la génération des numéros de documents.
    |
    */

    'numbering' => [
        'order_prefix' => env('ORDER_PREFIX', 'ORD-'),
        'invoice_prefix' => env('INVOICE_PREFIX', 'INV-'),
        'quote_prefix' => env('QUOTE_PREFIX', 'QT-'),
        'credit_note_prefix' => env('CREDIT_NOTE_PREFIX', 'CR-'),
        'number_padding' => 5, // Nombre de chiffres (ex: 00001)
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres des Documents
    |--------------------------------------------------------------------------
    |
    | Configuration des délais et paramètres par défaut des documents.
    |
    */

    'documents' => [
        // Délai de paiement par défaut (en jours)
        'payment_due_days' => env('PAYMENT_DUE_DAYS', 30),
        
        // Validité des devis (en jours)
        'quote_validity_days' => env('QUOTE_VALIDITY_DAYS', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Informations de l'Entreprise
    |--------------------------------------------------------------------------
    |
    | Informations affichées sur les documents PDF.
    |
    */

    'company' => [
        'name' => env('COMPANY_NAME', 'MisericordeAlu'),
        'address' => env('COMPANY_ADDRESS', ''),
        'phone' => env('COMPANY_PHONE', ''),
        'email' => env('COMPANY_EMAIL', ''),
        'siret' => env('COMPANY_SIRET', ''),
        'tva_number' => env('COMPANY_TVA_NUMBER', ''),
    ],

];
