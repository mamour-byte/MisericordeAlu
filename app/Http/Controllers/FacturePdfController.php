<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use PDF;

class FacturePdfController extends Controller
{
    public function generate($id)
    {
        $order = Order::with(['items',])->findOrFail($id);

        $produitsArray = $order->items->map(function ($item) {
            $quantity = $item->quantity ?? 0; 
            $price = $item->product->price ?? 0;

            return [
                'nom' => $item->product->name ?? 'Produit inconnu',
                'quantity' => $quantity,
                'prix_unitaire' => $price,
                'total_ligne' => $quantity * $price
            ];
        });

        $subtotal = $order->items->sum(function ($item) {
            return $item->quantity * ($item->product->price ?? 0);
        });

        $taxRate = 18;
        $factureTvaIncluse = $order->invoices->tva ?? false; 

        $taxAmount = $factureTvaIncluse ? $subtotal * ($taxRate / 100) : 0;
        $totalAmount = $subtotal + $taxAmount;

        $tva_status = $factureTvaIncluse ? 'TVA incluse' : 'TVA non incluse';

        $pdfData = [
            'numero_facture' => $order->facture->no_invoice ?? '-',
            'date_facture' => $order->facture->created_at ?? now()->format('Y-m-d'),
            'date_echeance' => $order->facture->date_echeance ?? now()->addDays(30)->format('Y-m-d'),

            'client_nom' => $order->customer_name ?? '',
            'client_adresse' => $order->customer_address?? '',
            'client_telephone' => $order->customer_phone ?? '',
            'client_email' => $order->customer_email ?? '',

            'produits' => $produitsArray,
            'subtotal' => $subtotal,
            'taxRate' => $taxRate,
            'taxAmount' => $taxAmount,
            'totalAmount' => $totalAmount,
            'tva_status' => $tva_status,

            'type_document' => ucfirst($order->facture->type_document ?? ''), 
        ];

        $pdf = PDF::loadView('pdf.facturepdf', $pdfData);
        return $pdf->stream('invoices ' . $order->customer_name . ' ' . now()->translatedFormat('F Y') . '.pdf');

    }
}
