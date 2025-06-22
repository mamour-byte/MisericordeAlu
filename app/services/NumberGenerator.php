<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Models\InvoiceItem;
use App\Models\QuoteItem;

class NumberGenerator
{
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD-';
        $lastNumber = OrderItem::whereNotNull('no_order')
            ->orderByDesc('id')
            ->value('no_order');

        $number = $lastNumber
            ? ((int) str_replace($prefix, '', $lastNumber)) + 1
            : 1;

        return $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public static function generateDocumentNumber(string $type): string
    {
        $prefix = $type === 'Invoice' ? 'INV-' : 'QT-';

        $lastNumber = $type === 'Invoice'
            ? InvoiceItem::whereNotNull('no_invoice')->orderByDesc('id')->value('no_invoice')
            : QuoteItem::whereNotNull('no_quote')->orderByDesc('id')->value('no_quote');

        $number = $lastNumber
            ? ((int) str_replace($prefix, '', $lastNumber)) + 1
            : 1;

        return $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public static function generateCreditNoteNumber(): string
    {
        $prefix = 'CR-';
        $lastNumber = \App\Models\Avoir::orderByDesc('id')->value('no_avoir');
        $number = $lastNumber ? ((int) str_replace($prefix, '', $lastNumber)) + 1 : 1;

        return $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

}
