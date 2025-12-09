<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerOrderController extends Controller
{
    // View order history
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
                       ->withCount('items')
                       ->latest()
                       ->get();

        return view('orders.index', compact('orders'));
    }

    // View specific order - ALWAYS show order details page
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('items.product', 'items.variant');
        
        // Load order items with product info
        $orderItems = OrderItem::where('order_id', $order->id)
            ->with('product')
            ->get();
        
        // Calculate totals
        $subtotal = $orderItems->sum(function($item) {
            return $item->price * $item->quantity;
        });
        
        // Determine fees
        if ($order->payment_method == 'cash_on_delivery') {
            $fee = $order->cod_fee ?? 0;
            $feeType = 'COD Fee';
        } else {
            $fee = $order->shipping_fee ?? 0;
            $feeType = 'Shipping Fee';
        }
        
        $calculatedTotal = $subtotal + $fee;

        return view('orders.show', compact('order', 'orderItems', 'subtotal', 'fee', 'feeType', 'calculatedTotal'));
    }
    
    // Payment Success page (ONLY for PayPal payments after checkout)
    public function paymentSuccess(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Only show payment success for PayPal payments
        if ($order->payment_method !== 'paypal') {
            return redirect()->route('orders.show', $order->id);
        }

        $order->load('items.product', 'items.variant');
        
        // Load order items
        $orderItems = OrderItem::where('order_id', $order->id)
            ->with('product')
            ->get();
        
        // Calculate totals
        $subtotal = $orderItems->sum(function($item) {
            return $item->price * $item->quantity;
        });
        
        $shippingFee = $order->shipping_fee ?? 0;
        $calculatedTotal = $subtotal + $shippingFee;

        return view('payment.success', compact('order', 'orderItems', 'subtotal', 'shippingFee', 'calculatedTotal'));
    }
    
    // COD Confirmation page (ONLY for COD payments after checkout)
    public function confirmation(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Only show confirmation for COD payments
        if ($order->payment_method !== 'cash_on_delivery') {
            return redirect()->route('orders.show', $order->id);
        }

        $order->load('items.product', 'items.variant');
        
        // Load order items
        $orderItems = OrderItem::where('order_id', $order->id)
            ->with('product')
            ->get();
        
        // Calculate totals
        $subtotal = $orderItems->sum(function($item) {
            return $item->price * $item->quantity;
        });
        
        $codFee = $order->cod_fee ?? 0;
        $calculatedTotal = $subtotal + $codFee;

        return view('orders.confirmation', compact('order', 'orderItems', 'subtotal', 'codFee', 'calculatedTotal'));
    }

    // Cancel order
    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow cancellation if order is still pending/processing
        if (!in_array($order->order_status, ['pending', 'processing'])) {
            return back()->with('error', 'This order can no longer be cancelled.');
        }

        DB::transaction(function () use ($order) {
            $order->update([
                'order_status' => 'cancelled',
                'payment_status' => 'cancelled'
            ]);

            // Restore stock if order is cancelled
            foreach ($order->items as $item) {
                $variant = ProductVariant::find($item->variant_id);
                if ($variant) {
                    $variant->increment('stock', $item->quantity);
                }
            }
        });

        return back()->with('success', 'Order cancelled successfully.');
    }
}