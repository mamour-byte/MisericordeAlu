<?php 

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use Orchid\Support\Facades\Toast;
use PDF;

class ExportPdfController extends Controller
{
    /**
     * Download the low stock products as a PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadLowStockPdf()
        {

            $shop = auth()->user()->shop;
            if (!$shop) {
                Toast::error("Aucun magasin ne vous a été attribué.");
                return redirect()->back();
            }

            $products = Product::with(['stockMovements', 'category'])
                                ->where('shop_id', $shop->id)
                                ->get();

            $productsWithStock = $products->map(function ($product) use ($shop) {
                $entries = $product->stockMovements
                    ->where('shop_id', $shop->id)
                    ->where('type', 'entry')
                    ->sum('quantity');
                $exits = $product->stockMovements
                    ->where('shop_id', $shop->id)
                    ->where('type', 'exit')
                    ->sum('quantity');
                $product->calculated_stock = $entries - $exits;
                return $product;
            })->filter(function ($product) {
                return $product->calculated_stock <= $product->stock_min;
            });

            if ($productsWithStock->isEmpty()) {
                Toast::info('Aucun produit sous le seuil de stock.');
                return redirect()->back();
            }

            $pdf = \PDF::loadView('pdf.low_stock_order', [
                'products' => $productsWithStock,
                'shop' => $shop,
            ]);

            return $pdf->download('bon_de_commande.pdf');
        }

}

