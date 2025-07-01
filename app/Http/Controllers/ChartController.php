<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class ChartController extends Controller
{
    public function MeilleurVenteMois()
    {
        $bestSeller = OrderItem::select('Order_items.product_id', DB::raw('SUM(Order_items.quantity) as total_sold'))
            ->join('Orders', 'Orders.id', '=', 'Order_items.Order_id')
            ->where('Orders.status', 'approved')
            ->where('archived', '!=', 'oui')
            ->whereMonth('Order_items.created_at', Carbon::now()->month)
            ->whereYear('Order_items.created_at', Carbon::now()->year)
            ->groupBy('Order_items.product_id')
            ->OrderByDesc('total_sold')
            ->with('product')
            ->first();

        return [
            'value' => $bestSeller ? $bestSeller->product->name : 'Aucune vente',
            // 'diff'  => $bestSeller ? $bestSeller->total_sold : 0,
        ];
    }

    public function MeilleurClientMois()
    {
        $best = Order::select('customer_name', DB::raw('SUM(total_amount) as total_spent'))
            ->whereMonth('created_at', now()->month)
            ->where('status', 'approved')
            ->where('archived', '!=', 'oui')
            ->groupBy('customer_name')
            ->OrderByDesc('total_spent')
            ->first();

        return [
            'value' => $best ? $best->customer_name : 'Aucun client',
            // 'diff'  => $best ? (float) $best->total_spent : 0,
        ];
    }

    public function VentesJour()
    {
        $count = Order::whereDate('created_at', Carbon::today())
            ->where('status', 'approved')
            ->where('archived', '!=', 'oui')
            ->count();

        return [
            'value' => $count,
            'diff'  => 0,
        ];
    }

    public function TotalJour()
    {
        $total = Order::whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay(),
            ])
            ->where('status', 'approved')
            ->where('archived', '!=', 'oui')
            ->sum('total_amount');

        return [
            'value' => (float) $total,
            'diff'  => 0,
        ];
    }

    
    public function TotalMois()
    {
        $total = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'approved')
            ->where('archived', '!=', 'oui')
            ->sum('total_amount');

        return [
            'value' => (float) $total,
            'diff'  => 0,
        ];
    }

    public function VentesParProduit(): array
    {
        $ventesParProduit = OrderItem::select('Order_items.product_id', DB::raw('SUM(Order_items.quantity) as total_ventes'))
            ->join('Orders', 'Orders.id', '=', 'Order_items.Order_id')
            ->where('Orders.status', 'approved')
            ->where('archived', '!=', 'oui')
            ->whereMonth('Order_items.created_at', Carbon::now()->month)
            ->whereYear('Order_items.created_at', Carbon::now()->year)
            ->with('product')
            ->groupBy('Order_items.product_id')
            ->take(7) // Limiter à 7 produits   
            ->get();

        return [[
            'labels' => $ventesParProduit->map(fn($item) => $item->product->name ?? 'Produit inconnu')->toArray(),
            'values' => $ventesParProduit->pluck('total_ventes')->toArray(),
        ]];
    }


        public function VentesSemaine(): array
        {
            $ventesParJour = Order::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(total_amount) as chiffre_affaire')
                )
                ->where('status', 'approved')
                ->where('archived', '!=', 'oui')
                ->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ])
                ->groupBy(DB::raw('DATE(created_at)'))
                ->OrderByDesc('chiffre_affaire') // Ordonner par chiffre d'affaires décroissant
                ->get();

            $joursSemaine = collect(Carbon::now()->startOfWeek()->daysUntil(Carbon::now()->endOfWeek()))
                ->mapWithKeys(fn($date) => [$date->format('Y-m-d') => 0]);

            foreach ($ventesParJour as $vente) {
                $date = $vente->date;
                $joursSemaine[$date] = $vente->chiffre_affaire;
            }

            $labels = $joursSemaine->keys()->map(fn($date) => Carbon::parse($date)->locale('fr')->isoFormat('dddd'))->toArray();
            $values = $joursSemaine->values()->toArray();

            return [[
                'labels' => $labels,
                'values' => $values,
            ]];
        }

        public function MeilleursVendeurs()
        {
            return User::select(
                    'users.id',
                    'users.name',
                    DB::raw('COUNT(Orders.id) as total_commandes'),
                    DB::raw('SUM(Orders.total_amount) as total_ventes')
                )
                ->join('Orders', 'users.id', '=', 'Orders.user_id')
                ->whereMonth('Orders.created_at', Carbon::now()->month)
                ->whereYear('Orders.created_at', Carbon::now()->year)
                ->where('Orders.status', 'approved')
                ->where('archived', '!=', 'oui')
                ->groupBy('users.id', 'users.name')
                ->OrderByDesc('total_ventes')
                ->take(5)
                ->get();
        }

        
    public function VentesParUser()
    {
        $ventesParUser = Order::select('user_id', DB::raw('SUM(total_amount) as total_ventes'))
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', 'approved')
            ->where('archived', '!=', 'oui')
            ->groupBy('user_id')
            ->with('user')
            ->get();

        return [[
            'labels' => $ventesParUser->map(fn($item) => $item->user->name ?? 'Utilisateur inconnu')->toArray(),
            'values' => $ventesParUser->pluck('total_ventes')->toArray(),
        ]];
    }


    public function VentesParVendeur($vendeurId)
        {
            $ventesParJour = Order::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(total_amount) as chiffre_affaire')
                )
                ->where('user_id', $vendeurId)
                ->where('status', 'approved')
                ->where('archived', '!=', 'oui')
                ->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ])
                ->groupBy(DB::raw('DATE(created_at)'))
                ->OrderByDesc('chiffre_affaire') // Ordonner par chiffre d'affaires décroissant
                ->get();

            $joursSemaine = collect(Carbon::now()->startOfWeek()->daysUntil(Carbon::now()->endOfWeek()))
                ->mapWithKeys(fn($date) => [$date->format('Y-m-d') => 0]);

            foreach ($ventesParJour as $vente) {
                $date = $vente->date;
                $joursSemaine[$date] = $vente->chiffre_affaire;
            }

            $labels = $joursSemaine->keys()->map(fn($date) => Carbon::parse($date)->locale('fr')->isoFormat('dddd'))->toArray();
            $values = $joursSemaine->values()->toArray();

            return [[
                'labels' => $labels,
                'values' => $values,
            ]];
        }


        
    public function VentesParMois(): array
        {
            $ventesParMois = \App\Models\Order::select(
                    DB::raw('MONTH(created_at) as mois'),
                    DB::raw('SUM(total_amount) as total_ventes')
                )
                ->whereYear('created_at', now()->year)
                ->where('status', 'approved')
                ->where('archived', '!=', 'oui')
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->orderBy('mois')
                ->get();

            // Générer les labels des mois (en français)
            $labels = [];
            $values = [];
            for ($i = 1; $i <= 12; $i++) {
                $labels[] = \Carbon\Carbon::create()->month($i)->locale('fr')->isoFormat('MMMM');
                $moisData = $ventesParMois->firstWhere('mois', $i);
                $values[] = $moisData ? (float)$moisData->total_ventes : 0;
            }

            return [[
                'labels' => $labels,
                'values' => $values,
            ]];
        }
}
