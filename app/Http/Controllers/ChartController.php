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
        $bestSeller = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('product_id')
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
        $best = Order::select('customer_name', DB::raw('SUM(total_amount) as total_spent'))
            ->whereMonth('created_at', now()->month)
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
        $count = Order::whereDate('created_at', Carbon::today())->count();

        return [
            'value' => $count,
            'diff'  => 0,
        ];
    }

    public function TotalSemaine()
    {
        $total = Order::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ])->sum('total_amount');

        return [
            'value' => (float) $total,
            'diff'  => 0,
        ];
    }

    public function TotalMois()
    {
        $total = Order::whereMonth('created_at', now()->month)->sum('total_amount');

        return [
            'value' => (float) $total,
            'diff'  => 0,
        ];
    }
    
    public function VentesParProduit(): array
    {
        $ventesParProduit = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_ventes'))
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->with('product')
            ->groupBy('product_id')
            ->take(10)
            ->get();

        return [[
            'labels' => $ventesParProduit->map(fn($item) => $item->product->name ?? 'Produit inconnu')->toArray(),
            'values' => $ventesParProduit->pluck('total_ventes')->toArray(),
        ]];
    }

    
    public function VentesSemaine(): array
        {
            $ventesParJour = OrderItem::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(quantity) as total_ventes')
                )
                ->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ])
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date')
                ->get();

            // Génère tous les jours de la semaine actuelle (lundi à dimanche)
            $joursSemaine = collect(Carbon::now()->startOfWeek()->daysUntil(Carbon::now()->endOfWeek()))
                ->mapWithKeys(fn($date) => [$date->format('Y-m-d') => 0]);

            // Fusionne avec les données réelles
            foreach ($ventesParJour as $vente) {
                $date = $vente->date;
                $joursSemaine[$date] = $vente->total_ventes;
            }

            // Optionnel : transformer les dates en noms de jour (lundi, mardi, ...)
            $labels = $joursSemaine->keys()->map(fn($date) => Carbon::parse($date)->locale('fr')->isoFormat('dddd'))->toArray();
            $values = $joursSemaine->values()->toArray();

            return [[
                'labels' => $labels,
                'values' => $values,
            ]];
        }

    public function MeilleursVendeurs()
        {
            return User::select('users.id', 'users.name', DB::raw('COUNT(orders.id) as total_commandes'), DB::raw('SUM(orders.total_amount) as total_ventes'))
                ->join('orders', 'users.id', '=', 'orders.user_id')
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('total_ventes')
                ->take(5) 
                ->get();
        }
    
    public function VentesParUser(){
        $ventesParUser = Order::select('user_id', DB::raw('SUM(total_amount) as total_ventes'))
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('user_id')
            ->with('user')
            ->get();

        return [[
            'labels' => $ventesParUser->map(fn($item) => $item->user->name ?? 'Utilisateur inconnu')->toArray(),
            'values' => $ventesParUser->pluck('total_ventes')->toArray(),
        ]];
    }

}
