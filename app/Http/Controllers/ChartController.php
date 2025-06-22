<?php

namespace App\Http\Controllers;

use App\Models\order;
use App\Models\orderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function MeilleurVenteMois()
    {
        $bestSeller = orderItem::select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'approved')
            ->where('archived', '!=', 'oui')
            ->whereMonth('order_items.created_at', Carbon::now()->month)
            ->whereYear('order_items.created_at', Carbon::now()->year)
            ->groupBy('order_items.product_id')
            ->orderByDesc('total_sold')
            ->with('product')
            ->first();

        return [
            'value' => $bestSeller ? $bestSeller->product->name : 'Aucune vente',
            'diff'  => $bestSeller ? $bestSeller->total_sold : 0,
        ];
    }

    public function MeilleurClientMois()
    {
        $best = order::select('customer_name', DB::raw('SUM(total_amount) as total_spent'))
            ->whereMonth('created_at', now()->month)
            ->where('status', 'approved')
            ->where('archived', '!=', 'oui')
            ->groupBy('customer_name')
            ->orderByDesc('total_spent')
            ->first();

        return [
            'value' => $best ? $best->customer_name : 'Aucun client',
            'diff'  => $best ? (float) $best->total_spent : 0,
        ];
    }

    public function VentesJour()
    {
        $count = order::whereDate('created_at', Carbon::today())
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
        $total = order::whereBetween('created_at', [
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
        $total = order::whereMonth('created_at', now()->month)
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
        $ventesParProduit = orderItem::select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total_ventes'))
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'approved')
            ->where('archived', '!=', 'oui')
            ->whereMonth('order_items.created_at', Carbon::now()->month)
            ->whereYear('order_items.created_at', Carbon::now()->year)
            ->with('product')
            ->groupBy('order_items.product_id')
            ->take(7) // Limiter à 7 produits   
            ->get();

        return [[
            'labels' => $ventesParProduit->map(fn($item) => $item->product->name ?? 'Produit inconnu')->toArray(),
            'values' => $ventesParProduit->pluck('total_ventes')->toArray(),
        ]];
    }


        public function VentesSemaine(): array
        {
            $ventesParJour = order::select(
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
                ->orderByDesc('chiffre_affaire') // Ordonner par chiffre d'affaires décroissant
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
                    DB::raw('COUNT(orders.id) as total_commandes'),
                    DB::raw('SUM(orders.total_amount) as total_ventes')
                )
                ->join('orders', 'users.id', '=', 'orders.user_id')
                ->where('orders.status', 'approved')
                ->where('archived', '!=', 'oui')
                ->whereMonth('orders.created_at', Carbon::now()->month)
                ->whereYear('orders.created_at', Carbon::now()->year)
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('total_ventes')
                ->take(5)
                ->get();
        }

        
    public function VentesParUser()
    {
        $ventesParUser = order::select('user_id', DB::raw('SUM(total_amount) as total_ventes'))
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
            $ventesParJour = order::select(
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
                ->orderByDesc('chiffre_affaire') // Ordonner par chiffre d'affaires décroissant
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
}
