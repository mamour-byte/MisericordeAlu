<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Fabrication;
use PDF;

class FacturePdfController extends Controller
{
    /**
     * Generate a PDF invoice/quote for an order.
     *
     * @param int $id The order ID
     * @return \Illuminate\Http\Response PDF stream response
     */
    public function generate($id)
    {
        $order = Order::with(['items.product'])->findOrFail($id);

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

        // Utiliser la configuration au lieu de valeurs hardcodées
        $taxRate = config('business.tax.rate', 18);
        $paymentDueDays = config('business.documents.payment_due_days', 30);
        
        $factureTvaIncluse = $order->invoice?->tva ?? false;

        $remise = (float) ($order->remise ?? 0);

        // Apply remise (discount) before tax
        $subtotalAfterRemise = max(0, $subtotal - $remise);

        $taxAmount = $factureTvaIncluse ? $subtotalAfterRemise * ($taxRate / 100) : 0;
        $totalAmount = $subtotalAfterRemise + $taxAmount;

        $tva_status = $factureTvaIncluse ? 'TVA incluse' : 'TVA non incluse';

        $type_document = $this->determineDocumentType($order);

        // Récupérer le numéro de document depuis order_items
        $numero_Doc = $order->items->first()->no_order ?? '-';

        $pdfData = [
            'numero_Doc' => $numero_Doc ?? '-',
            'date_facture' => $order->invoice?->created_at?->format('Y-m-d') ?? $order->created_at->format('Y-m-d'),
            'date_echeance' => $order->invoice?->date_echeance ?? now()->addDays($paymentDueDays)->format('Y-m-d'),

            'client_nom' => $order->customer_name ?? '',
            'client_adresse' => $order->customer_address ?? '',
            'client_telephone' => $order->customer_phone ?? '',
            'client_email' => $order->customer_email ?? '',

            'produits' => $produitsArray,
            'subtotal' => $subtotal,
            'remise' => $remise,
            'subtotal_after_remise' => $subtotalAfterRemise,
            'taxRate' => $taxRate,
            'taxAmount' => $taxAmount,
            'total' => $subtotal,
            'totalAmount' => $totalAmount,
            'tva_status' => $tva_status,

            'type_document' => $type_document,
            
            // Informations de l'entreprise depuis la configuration
            'company' => config('business.company'),
        ];

        $pdf = PDF::loadView('pdf.facturepdf', $pdfData);
        return $pdf->stream('invoices ' . $order->customer_name . ' ' . now()->translatedFormat('F Y') . '.pdf');
    }

    /**
     * Determine the document type based on order relationships.
     *
     * @param Order $order
     * @return string
     */
    private function determineDocumentType(Order $order): string
    {
        if (!is_null($order->quote_id) && is_null($order->invoice_id)) {
            return 'Devis';
        } elseif (!is_null($order->invoice_id) && is_null($order->quote_id)) {
            return 'Facture';
        }
        return 'Document';
    }


    /**
     * Generate a PDF quote for a fabrication order.
     *
     * @param int $id The fabrication ID
     * @return \Illuminate\Http\Response PDF stream response
     */
    public function generateQuote($id)
    {
        $fabrication = Fabrication::with(['items'])->findOrFail($id);

        // Utiliser la configuration au lieu de valeurs hardcodées
        $taxRate = config('business.tax.rate', 18);
        $quoteValidityDays = config('business.documents.quote_validity_days', 30);

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
                'total_ligne' => $quantity * $price_meter * ($width * 0.01) * ($height * 0.01),
            ];
        });

        // Calcul total
        $subtotal = $fabrication->items->sum(function ($item) {
            return $item->quantity * ($item->price_meter ?? 0) * ($item->width * 0.01 ?? 0) * ($item->height * 0.01 ?? 0);
        });

        $tvaIncluse = $fabrication->tva ?? false;

        $taxAmount = $tvaIncluse ? $subtotal * ($taxRate / 100) : 0;
        $totalAmount = $subtotal + $taxAmount;

        $tva_status = $tvaIncluse ? 'TVA incluse' : 'TVA non incluse';
        $numeroDevis = 'DV' . '-' . now()->format('md') . '00' . $fabrication->id;

        $type_document = match($fabrication->status) {
            'quote' => 'Devis',
            'invoice' => 'Facture',
            default => 'Document',
        };

        // Préparation des données pour le PDF
        $pdfData = [
            'numero_facture' => $numeroDevis ?? '-',
            'date_facture' => $fabrication->created_at->format('Y-m-d') ?? now()->format('Y-m-d'),
            'date_echeance' => $fabrication->date_echeance ?? now()->addDays($quoteValidityDays)->format('Y-m-d'),

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
            
            // Informations de l'entreprise depuis la configuration
            'company' => config('business.company'),
        ];

        // Génération du PDF
        $pdf = PDF::loadView('pdf.devispdf', $pdfData);
        return $pdf->stream('devis_' . $fabrication->customer_name . '_' . now()->translatedFormat('F_Y') . '.pdf');
    }
}
