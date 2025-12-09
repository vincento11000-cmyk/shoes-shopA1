<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\ProductVariant;

class PaymentController extends Controller
{
    /**
     * Process PayPal Payment
     */
    public function pay(Request $request)
    {
        // Validate checkout fields
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // 1. Get user's cart from database
            $cart = Cart::where('user_id', Auth::id())->first();
            
            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
            }

            $items = $cart->items()->with(['product', 'variant'])->get();
            
            // Calculate total
            $total = $items->sum(function ($item) {
                return $item->product->base_price * $item->quantity;
            });

            // 2. Create the order (Pending status)
            $order = Order::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'notes' => $request->notes,
                'total_amount' => $total,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'payment_method' => 'paypal'
            ]);

            // 3. Save order items
            foreach ($items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'variant_id' => $cartItem->variant_id,
                    'size' => $cartItem->variant->size,
                    'color' => $cartItem->variant->color,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->base_price
                ]);
            }

            // 4. Create PayPal Order
            $paypalOrder = $this->createPayPalOrder($total, $order->id);

            Log::info('PayPal Order Created', [
                'order_id' => $order->id,
                'paypal_order_id' => $paypalOrder['id'],
                'status' => $paypalOrder['status']
            ]);

            // 5. Get approval URL
            $approveUrl = null;
            foreach ($paypalOrder['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    $approveUrl = $link['href'];
                    break;
                }
            }

            if (!$approveUrl) {
                throw new \Exception('Could not get PayPal approval URL');
            }

            // 6. SAVE PAYPAL ORDER ID TO DATABASE - ENSURED METHOD
            $paypalOrderId = $paypalOrder['id'];
            
            Log::info('Attempting to save PayPal Order ID to database', [
                'order_id' => $order->id,
                'paypal_order_id' => $paypalOrderId
            ]);

            // Method 1: Direct database update (most reliable)
            $updateResult = DB::table('orders')
                ->where('id', $order->id)
                ->update(['paypal_order_id' => $paypalOrderId]);

            Log::info('Direct DB Update Result', [
                'affected_rows' => $updateResult
            ]);

            // Method 2: Verify the save worked
            $verifiedOrder = DB::table('orders')->where('id', $order->id)->first();
            Log::info('Verification - Stored PayPal Order ID:', [
                'stored_value' => $verifiedOrder->paypal_order_id ?? 'NULL'
            ]);

            if (($verifiedOrder->paypal_order_id ?? null) !== $paypalOrderId) {
                throw new \Exception('Failed to save PayPal order ID to database');
            }

            // 7. Store in session for redundancy
            session(['current_paypal_order_id' => $paypalOrderId]);
            session(['current_order_id' => $order->id]);

            Log::info('Payment setup completed', [
                'database_paypal_id' => $verifiedOrder->paypal_order_id,
                'session_paypal_id' => session('current_paypal_order_id'),
                'redirecting_to' => $approveUrl
            ]);

            DB::commit();
            return redirect($approveUrl);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PayPal Order Creation Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error creating payment: ' . $e->getMessage());
        }
    }

    /**
     * PayPal Success Callback
     */
    public function success(Request $request)
    {
        DB::beginTransaction();

        try {
            // Get token from URL (this is the PayPal order ID)
            $token = $request->query('token');
            
            Log::info('PayPal Success Callback Started', [
                'token' => $token,
                'session_data' => [
                    'order_id' => session('current_order_id'),
                    'paypal_id' => session('current_paypal_order_id')
                ]
            ]);

            if (!$token) {
                throw new \Exception('Missing payment token from PayPal');
            }

            // Try multiple methods to find the order
            $order = null;
            $foundBy = '';
            
            // Method 1: Find by PayPal order ID in database
            $order = Order::where('paypal_order_id', $token)->first();
            if ($order) {
                $foundBy = 'database_paypal_id';
                Log::info('Found order by PayPal ID in database', [
                    'order_id' => $order->id,
                    'paypal_order_id' => $order->paypal_order_id
                ]);
            }
            
            // Method 2: If Method 1 fails, try session
            if (!$order && session('current_paypal_order_id') === $token) {
                $orderId = session('current_order_id');
                $order = Order::find($orderId);
                if ($order) {
                    $foundBy = 'session_fallback';
                    Log::info('Found order via session fallback', [
                        'order_id' => $orderId,
                        'paypal_order_id' => $token
                    ]);
                }
            }
            
            // Method 3: If still not found, try to find by recent orders for this user
            if (!$order) {
                $order = Order::where('user_id', Auth::id())
                             ->where('payment_status', 'pending')
                             ->where('payment_method', 'paypal')
                             ->latest()
                             ->first();
                if ($order) {
                    $foundBy = 'recent_order_fallback';
                    Log::info('Found order via recent order fallback', [
                        'order_id' => $order->id,
                        'current_paypal_id_in_db' => $order->paypal_order_id
                    ]);
                }
            }

            if (!$order) {
                // Debug: Check what's actually in the database
                $allOrders = Order::where('user_id', Auth::id())->get();
                Log::error('ORDER FINDING FAILED - Debug Info', [
                    'searched_token' => $token,
                    'total_orders_for_user' => $allOrders->count(),
                    'orders_with_paypal_ids' => $allOrders->whereNotNull('paypal_order_id')->pluck('paypal_order_id', 'id'),
                    'pending_paypal_orders' => Order::where('user_id', Auth::id())
                                                  ->where('payment_method', 'paypal')
                                                  ->where('payment_status', 'pending')
                                                  ->get()->pluck('id', 'paypal_order_id')
                ]);
                throw new \Exception('Order not found for PayPal order ID: ' . $token);
            }

            Log::info('Order found successfully', [
                'order_id' => $order->id,
                'method_used' => $foundBy,
                'stored_paypal_id' => $order->paypal_order_id,
                'current_payment_status' => $order->payment_status
            ]);

            // *** SIMPLE FIX: Check if order is already paid ***
            if ($order->payment_status === 'paid') {
                Log::info('Order already paid, showing success page', [
                    'order_id' => $order->id
                ]);
                
                // Clear session to prevent re-processing
                session()->forget(['current_paypal_order_id', 'current_order_id']);
                
                DB::commit();
                return view('payment.success', compact('order'));
            }

            // Rest of your success method remains the same...
            // Verify this order belongs to the logged-in user
            if ($order->user_id !== Auth::id()) {
                abort(403, 'Unauthorized');
            }

            // Capture the payment
            $captureResult = $this->capturePayPalPayment($token);

            Log::info('PayPal Capture Result', [
                'status' => $captureResult['status'] ?? 'NO_STATUS'
            ]);

            if ($captureResult['status'] === 'COMPLETED') {
                // Update order status
                $order->update([
                    'payment_status' => 'paid',
                    'order_status' => 'processing'
                ]);

                // Deduct stock from variants
                foreach ($order->items as $item) {
                    $variant = ProductVariant::find($item->variant_id);
                    if ($variant) {
                        $variant->stock -= $item->quantity;
                        $variant->save();
                    }
                }

                // Clear user's cart
                $cart = Cart::where('user_id', Auth::id())->first();
                if ($cart) {
                    $cart->items()->delete();
                }

                // Clear session
                session()->forget(['current_paypal_order_id', 'current_order_id']);

                DB::commit();
                
                Log::info('PayPal Payment Completed Successfully', [
                    'order_id' => $order->id,
                    'payment_status' => 'paid'
                ]);
                
                return view('payment.success', compact('order'));
            }

            throw new \Exception('Payment not completed. Status: ' . ($captureResult['status'] ?? 'UNKNOWN'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PayPal Payment Success Error: ' . $e->getMessage());
            return redirect()->route('checkout.index')->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }

    /**
     * PayPal Cancel Callback
     */
    public function failed(Request $request)
    {
        try {
            $token = $request->query('token');
            
            if ($token) {
                $order = Order::where('paypal_order_id', $token)->first();
                
                if (!$order && session('current_paypal_order_id') === $token) {
                    $orderId = session('current_order_id');
                    $order = Order::find($orderId);
                }
                
                if ($order && $order->user_id === Auth::id()) {
                    $order->update([
                        'payment_status' => 'cancelled',
                        'order_status' => 'cancelled'
                    ]);
                }
            }

            session()->forget(['current_paypal_order_id', 'current_order_id']);

            return redirect()->route('checkout')->with('error', 'Payment was cancelled.');

        } catch (\Exception $e) {
            Log::error('PayPal Cancel Error: ' . $e->getMessage());
            return redirect()->route('checkout')->with('error', 'Payment was cancelled.');
        }
    }

    /**
     * PayPal Service Methods
     */
    private function getPayPalAccessToken()
    {
        try {
            $clientId = config('paypal.client_id');
            $clientSecret = config('paypal.client_secret');
            $mode = config('paypal.settings.mode', 'sandbox');
            $baseUrl = $mode === 'live' ? 'https://api.paypal.com' : 'https://api.sandbox.paypal.com';

            if (empty($clientId) || empty($clientSecret)) {
                throw new \Exception('PayPal credentials are missing.');
            }

            $response = Http::withBasicAuth($clientId, $clientSecret)
                ->asForm()
                ->post($baseUrl . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials'
                ]);

            if ($response->successful()) {
                $tokenData = $response->json();
                Log::info('PayPal Access Token Obtained', [
                    'token_type' => $tokenData['token_type'] ?? 'N/A',
                    'expires_in' => $tokenData['expires_in'] ?? 'N/A'
                ]);
                return $tokenData['access_token'];
            }

            Log::error('PayPal Access Token Error - Status: ' . $response->status() . ' Response: ' . $response->body());
            throw new \Exception('Could not get PayPal access token. Status: ' . $response->status());

        } catch (\Exception $e) {
            Log::error('PayPal Token Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    private function createPayPalOrder($amount, $orderId)
    {
        try {
            $mode = config('paypal.settings.mode', 'sandbox');
            $baseUrl = $mode === 'live' ? 'https://api.paypal.com' : 'https://api.sandbox.paypal.com';
            $accessToken = $this->getPayPalAccessToken();

            $returnUrl = route('payment.success');
            $cancelUrl = route('payment.failed');

            $requestData = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => 'order_' . $orderId,
                        'amount' => [
                            'currency_code' => 'PHP',
                            'value' => number_format($amount, 2, '.', '')
                        ]
                    ]
                ],
                'application_context' => [
                    'brand_name' => config('app.name', 'Shoe Shop'),
                    'user_action' => 'PAY_NOW',
                    'return_url' => $returnUrl,
                    'cancel_url' => $cancelUrl,
                    'shipping_preference' => 'NO_SHIPPING'
                ]
            ];

            Log::info('Creating PayPal Order', [
                'request_data' => $requestData
            ]);

            $response = Http::withToken($accessToken)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Prefer' => 'return=representation'
                ])
                ->post($baseUrl . '/v2/checkout/orders', $requestData);

            if ($response->successful()) {
                $orderData = $response->json();
                Log::info('PayPal Order Created Successfully', [
                    'paypal_order_id' => $orderData['id'],
                    'status' => $orderData['status']
                ]);
                return $orderData;
            }

            Log::error('PayPal Create Order Error - Status: ' . $response->status() . ' Response: ' . $response->body());
            throw new \Exception('Could not create PayPal order. Status: ' . $response->status());

        } catch (\Exception $e) {
            Log::error('PayPal Create Order Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    private function capturePayPalPayment($orderID)
    {
        try {
            $mode = config('paypal.settings.mode', 'sandbox');
            $baseUrl = $mode === 'live' ? 'https://api.paypal.com' : 'https://api.sandbox.paypal.com';
            $accessToken = $this->getPayPalAccessToken();

            Log::info('Attempting to capture PayPal payment', [
                'paypal_order_id' => $orderID,
                'base_url' => $baseUrl
            ]);

            // For capture, we send an empty JSON object as required by PayPal API
            $response = Http::withToken($accessToken)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Prefer' => 'return=representation'
                ])
                ->post($baseUrl . '/v2/checkout/orders/' . $orderID . '/capture', (object)[]);

            Log::info('PayPal Capture Response - Status: ' . $response->status());

            if ($response->successful()) {
                $captureData = $response->json();
                Log::info('PayPal Capture Successful', [
                    'status' => $captureData['status'] ?? 'UNKNOWN',
                    'capture_id' => $captureData['purchase_units'][0]['payments']['captures'][0]['id'] ?? 'NO_ID'
                ]);
                return $captureData;
            }

            // Log detailed error information
            $errorStatus = $response->status();
            $errorBody = $response->body();
            
            Log::error('PayPal Capture Failed', [
                'status_code' => $errorStatus,
                'response_body' => $errorBody,
                'paypal_order_id' => $orderID
            ]);

            throw new \Exception('Could not capture PayPal payment. Status: ' . $errorStatus . ' - ' . $errorBody);

        } catch (\Exception $e) {
            Log::error('PayPal Capture Exception: ' . $e->getMessage());
            throw $e;
        }
    }
}