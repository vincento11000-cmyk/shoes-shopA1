<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\ProductVariant;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(WeatherService $weatherService)
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $items = $cart->items()->with(['product', 'variant'])->get();
        $total = $items->sum(function ($item) {
            return $item->product->base_price * $item->quantity;
        });

        // Get weather warning
        $weatherWarning = $weatherService->getWeatherWarning();

        return view('checkout.index', compact('items', 'total', 'weatherWarning'));
    }

    /**
     * Process Cash on Delivery (COD) order
     */
public function processCOD(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string',
        'notes' => 'nullable|string',
    ]);

    $cart = Cart::where('user_id', Auth::id())->first();
    
    if (!$cart || $cart->items->isEmpty()) {
        return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
    }

    DB::beginTransaction();

    try {
        $items = $cart->items()->with(['product', 'variant'])->get();
        
        // Calculate total with COD fee
        $subtotal = $items->sum(function ($item) {
            return $item->product->base_price * $item->quantity;
        });
        $codFee = 50; // ₱50 COD fee
        $totalWithCOD = $subtotal + $codFee;

        // Create order with COD details - USING CORRECT FIELD NAMES
        $order = Order::create([
            'user_id' => Auth::id(),
            'name' => $request->name,       // Keep as 'name' since your model uses this
            'phone' => $request->phone,     // Keep as 'phone'
            'address' => $request->address, // Keep as 'address'
            'notes' => $request->notes,
            'total_amount' => $totalWithCOD,
            'payment_method' => 'cash_on_delivery',
            'payment_status' => 'pending',
            'order_status' => 'pending',
            'cod_fee' => $codFee,
        ]);

        foreach ($items as $cartItem) {
            $variant = $cartItem->variant;

            // Check stock
            if (!$variant || $variant->stock < $cartItem->quantity) {
                DB::rollBack();
                return redirect()->back()->with('error', "Insufficient stock for {$cartItem->product->name} - Size: {$variant->size}, Color: {$variant->color}");
            }

            // Deduct stock immediately for COD orders too
            $variant->stock -= $cartItem->quantity;
            $variant->save();

            // Create order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'variant_id' => $cartItem->variant_id,
                'size' => $variant->size,
                'color' => $variant->color,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->product->base_price,
            ]);
        }

        // Clear cart
        $cart->items()->delete();
        DB::commit();

        // Redirect to COD confirmation page
        return redirect()->route('orders.confirmation', $order)
            ->with('success', 'COD order placed successfully! Please prepare ₱' . number_format($totalWithCOD, 2) . ' for cash payment upon delivery. Your order ID is #' . $order->id);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}
    /**
     * Old store method - consider removing or keeping for reference
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'required|string',
        ]);

        $cart = Cart::where('user_id', Auth::id())->first();
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        DB::beginTransaction();

        try {
            $items = $cart->items()->with(['product', 'variant'])->get();
            $total = $items->sum(function ($item) {
                return $item->product->base_price * $item->quantity;
            });

            // Create order (for credit_card method - old method)
            $order = Order::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'notes' => $request->notes,
                'total_amount' => $total,
                'payment_method' => 'credit_card', // Old payment method
                'payment_status' => 'pending',
                'order_status' => 'pending'
            ]);

            foreach ($items as $cartItem) {
                $variant = $cartItem->variant;

                // Check stock
                if (!$variant || $variant->stock < $cartItem->quantity) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Insufficient stock for {$cartItem->product->name} - Size: {$variant->size}, Color: {$variant->color}");
                }

                // Deduct stock
                $variant->stock -= $cartItem->quantity;
                $variant->save();

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'variant_id' => $cartItem->variant_id,
                    'size' => $variant->size,
                    'color' => $variant->color,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->base_price
                ]);
            }

            // Clear cart
            $cart->items()->delete();
            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully! Your order ID is #' . $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Process PayPal payment - This should be handled by PaymentController
     * You can remove this method since you have a separate PaymentController
     */
    public function processPayPal(Request $request)
    {
        // This method is handled by PaymentController::pay()
        // Redirect to payment controller
        return redirect()->route('checkout.pay');
    }
}