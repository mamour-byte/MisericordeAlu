<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Models\InvoiceItem;
use App\Models\QuoteItem;
use Illuminate\Support\Facades\DB;

class NumberGenerator
{
    /**
     * Generate a unique order number with transaction lock to prevent race conditions.
     *
     * @return string The generated order number (e.g., ORD-00001)
     */
    public static function generateOrderNumber(): string
    {
        return DB::transaction(function () {
            $prefix = config('business.numbering.order_prefix', 'ORD-');
            $padding = config('business.numbering.number_padding', 5);
            
            $lastNumber = OrderItem::whereNotNull('no_order')
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('no_order');

            $number = $lastNumber
                ? ((int) str_replace($prefix, '', $lastNumber)) + 1
                : 1;

            return $prefix . str_pad($number, $padding, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Generate a unique document number (Invoice or Quote) with transaction lock.
     *
     * @param string $type The document type ('Invoice' or 'Quote')
     * @return string The generated document number
     */
    public static function generateDocumentNumber(string $type): string
    {
        return DB::transaction(function () use ($type) {
            $prefix = $type === 'Invoice' 
                ? config('business.numbering.invoice_prefix', 'INV-')
                : config('business.numbering.quote_prefix', 'QT-');
            $padding = config('business.numbering.number_padding', 5);

            $lastNumber = $type === 'Invoice'
                ? InvoiceItem::whereNotNull('no_invoice')
                    ->lockForUpdate()
                    ->orderByDesc('id')
                    ->value('no_invoice')
                : QuoteItem::whereNotNull('no_quote')
                    ->lockForUpdate()
                    ->orderByDesc('id')
                    ->value('no_quote');

            $number = $lastNumber
                ? ((int) str_replace($prefix, '', $lastNumber)) + 1
                : 1;

            return $prefix . str_pad($number, $padding, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Generate a unique credit note number with transaction lock.
     *
     * @return string The generated credit note number (e.g., CR-00001)
     */
    public static function generateCreditNoteNumber(): string
    {
        return DB::transaction(function () {
            $prefix = config('business.numbering.credit_note_prefix', 'CR-');
            $padding = config('business.numbering.number_padding', 5);
            
            $lastNumber = \App\Models\Avoir::lockForUpdate()
                ->orderByDesc('id')
                ->value('no_avoir');
            
            $number = $lastNumber 
                ? ((int) str_replace($prefix, '', $lastNumber)) + 1 
                : 1;

            return $prefix . str_pad($number, $padding, '0', STR_PAD_LEFT);
        });
    }
}
