<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;

class CartService
{
    /**
     * Get the current user's cart
     */
    public function getUserCart($userId)
    {
        // Remove 'total' from firstOrCreate since column doesn't exist
        return Cart::firstOrCreate([
            'user_id' => $userId
        ]);
    }

    /**
     * Add to cart with stock validation
     */
    public function addToCart($userId, $productId, $variantId, $quantity)
    {
        $cart = $this->getUserCart($userId);

        // Check stock before adding
        $variant = ProductVariant::findOrFail($variantId);
        
        if ($variant->stock < $quantity) {
            throw new \Exception('Not enough stock available!');
        }

        // Check if same variant already exists
        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('variant_id', $variantId)
            ->first();

        if ($existingItem) {
            // Check total quantity against stock
            $totalQuantity = $existingItem->quantity + $quantity;
            if ($variant->stock < $totalQuantity) {
                throw new \Exception('Not enough stock available for additional quantity!');
            }
            
            // Increase quantity
            $existingItem->quantity = $totalQuantity;
            $existingItem->save();
        } else {
            // Create new item
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity'   => $quantity,
            ]);
        }

        return $cart; // Just return the cart, no totals to update
    }

    /**
     * Change quantity with stock validation
     */
    public function updateQuantity($cartItemId, $quantity)
    {
        $item = CartItem::with('variant')->findOrFail($cartItemId);
        
        // Check stock
        if ($item->variant->stock < $quantity) {
            throw new \Exception('Not enough stock available!');
        }
        
        $item->quantity = $quantity;
        $item->save();

        return $item->cart; // Just return the cart
    }

    /**
     * Remove item
     */
    public function removeItem($cartItemId)
    {
        $item = CartItem::findOrFail($cartItemId);
        $cart = $item->cart;

        $item->delete();

        return $cart; // Just return the cart
    }
    
    /**
     * Calculate cart total on the fly (optional helper)
     */
    public function calculateTotal($cart)
    {
        return $cart->items->sum(function ($item) {
            return $item->product->base_price * $item->quantity;
        });
    }
}