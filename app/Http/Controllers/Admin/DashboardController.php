<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get real data from your database
        $orders = Order::count();
        $customers = User::count();
        
        // Calculate total sales from completed orders
        $sales = Order::where('order_status', 'completed')
                     ->orWhere('payment_status', 'completed')
                     ->sum('total_amount') ?? 0;
        
        // Count low stock variants (less than 10 in stock)
        $low_stock = ProductVariant::where('stock', '<', 10)->count();

        return view('admin.dashboard', [
            'orders' => $orders,
            'customers' => $customers,
            'sales' => $sales,
            'low_stock' => $low_stock
        ]);
    }
}