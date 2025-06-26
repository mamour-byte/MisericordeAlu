<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Fabrication;
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

        if (!is_null($order->quote_id) && is_null($order->invoice_id)) {
                $type_document = 'Devis';
            } elseif (!is_null($order->invoice_id) && is_null($order->quote_id)) {
                $type_document = 'Facture';
            } else {
                $type_document = 'Document';
            }

            // Récupérer le numéro de document depuis order_items
            $numero_Doc = $order->items->first()->no_order ?? '-';


        $pdfData = [
            'numero_Doc' => $numero_Doc ?? '-',
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

            'type_document' => $type_document, 
        ];

        $pdf = PDF::loadView('pdf.facturepdf', $pdfData);
        return $pdf->stream('invoices ' . $order->customer_name . ' ' . now()->translatedFormat('F Y') . '.pdf');

    }


    public function generateQuote($id)
        {
            $fabrication = Fabrication::with(['items'])->findOrFail($id);

            // Génération des lignes de produit
                $produitsArray = $fabrication->items->map(function ($item) {
                $quantity = $item->quantity ?? 0; 
                $price_meter = $item->price_meter ?? 0;
                $width = $item->width ?? 0;
                $height = $item->height ?? 0;

                return [
                    'largeur' => $width,
                    'longueur' => $height,
                    'nom' => $item->type ?? 'Produit inconnu',
                    'quantity' => $quantity,
                    'price_meter' => $price_meter,
                    'total_ligne' => $quantity * $price_meter * ($width*0.01) * ($height*0.01) , // Conversion en mètres,
                ];
            });

            // Calcul total
            $subtotal = $fabrication->items->sum(function ($item) {
                return $item->quantity * ($item->price_meter ?? 0) * ($item->width*0.01 ?? 0) * ($item->height*0.01 ?? 0);
            });

            $taxRate = 18;
            $tvaIncluse = $fabrication->tva ?? false;

            $taxAmount = $tvaIncluse ? $subtotal * ($taxRate / 100) : 0;
            $totalAmount = $subtotal + $taxAmount;

            $tva_status = $tvaIncluse ? 'TVA incluse' : 'TVA non incluse';
            $numeroDevis = 'DV'.'-'.now()->format('md') . 00 . $fabrication->id;

            if($fabrication->status == 'quote') {
                $type_document = 'Devis';
            }elseif($fabrication->status == 'invoice') {
                $type_document = 'Facture';
            } else {
                $type_document = 'Document';
            }

            // Préparation des données pour le PDF
            $pdfData = [
                'numero_facture' => $numeroDevis ?? '-',
                'date_facture' => $fabrication->created_at->format('Y-m-d') ?? now()->format('Y-m-d'),
                'date_echeance' => $fabrication->date_echeance ?? now()->addDays(30)->format('Y-m-d'),

                'client_nom' => $fabrication->customer_name ?? '',
                'client_adresse' => $fabrication->customer_address ?? '',
                'client_telephone' => $fabrication->customer_phone ?? '',
                'client_email' => $fabrication->customer_email ?? '',

                'produits' => $produitsArray,
                'subtotal' => $subtotal,
                'taxRate' => $taxRate,
                'taxAmount' => $taxAmount,
                'totalAmount' => $totalAmount,
                'tva_status' => $tva_status,

                'type_document' => $type_document,
            ];

            // Génération du PDF
            $pdf = PDF::loadView('pdf.devispdf', $pdfData);
            return $pdf->stream('devis_' . $fabrication->customer_name . '_' . now()->translatedFormat('F_Y') . '.pdf');
        }

}
