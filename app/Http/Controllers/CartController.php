<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
{
    /** @var \App\Models\User $user */
    $user = Auth::user();
    
    $cart = $this->cartService->getUserCart($user->id);
    
    // Get items with relationships
    $items = $cart->items()->with(['product', 'variant'])->get();
    
    // Calculate total using the service or directly
    $total = $this->cartService->calculateTotal($cart);
    // OR calculate directly:
    // $total = $items->sum(function ($item) {
    //     return $item->product->base_price * $item->quantity;
    // });

    return view('cart.index', compact('items', 'total'));
}

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        try {
            $cart = $this->cartService->addToCart(
                $user->id,
                $request->product_id,
                $request->variant_id,
                $request->quantity
            );
            
            return back()->with('success', 'Product added to cart!');
            
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $this->cartService->updateQuantity($id, $request->quantity);
            return back()->with('success', 'Cart updated!');
            
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function remove($id)
    {
        try {
            $this->cartService->removeItem($id);
            return back()->with('success', 'Item removed from cart!');
            
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}