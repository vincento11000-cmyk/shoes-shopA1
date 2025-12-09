<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Order #{{ $order->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            #receipt { box-shadow: none !important; border: 1px solid #ddd !important; }
            .border { border-color: #ddd !important; }
            .max-w-4xl { max-width: 100% !important; }
            .p-8 { padding: 20px !important; }
            .gap-8 { gap: 16px !important; }
            .text-3xl { font-size: 24px !important; }
            .text-2xl { font-size: 20px !important; }
            .text-xl { font-size: 18px !important; }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="max-w-4xl mx-auto p-4 md:p-8">
        <!-- Receipt Card -->
        <div id="receipt" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            
            <!-- Receipt Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-8 text-white">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-lg">
                            <i class="fas fa-shoe-prints text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold">{{ config('app.name', 'Shoe Shop') }}</h1>
                            <p class="text-blue-100 text-sm mt-1">Official Receipt</p>
                        </div>
                    </div>
                    <div class="text-center md:text-right">
                        <div class="inline-block bg-white/20 backdrop-blur-sm rounded-lg px-5 py-3 border border-white/30">
                            <p class="text-blue-100 text-xs uppercase tracking-wider">Receipt #</p>
                            <p class="text-xl md:text-2xl font-bold">RCPT-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                            <p class="text-blue-100 text-xs mt-1">{{ now()->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Receipt Content -->
            <div class="p-6 md:p-8">
                <!-- Order & Customer Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                    <!-- Order Details -->
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Order Details</h2>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Order ID:</span>
                                    <span class="font-medium">#{{ $order->id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Order Date:</span>
                                    <span class="font-medium">{{ $order->created_at->format('F d, Y h:i A') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Payment Method:</span>
                                    <span class="font-medium capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Payment Status:</span>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                        @if($order->payment_status == 'paid') bg-green-100 text-green-800
                                        @elseif($order->payment_status == 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Order Status:</span>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                        @if($order->order_status == 'completed') bg-green-100 text-green-800
                                        @elseif($order->order_status == 'processing') bg-blue-100 text-blue-800
                                        @elseif($order->order_status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->order_status == 'shipped') bg-purple-100 text-purple-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($order->order_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Details -->
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Customer Details</h2>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Customer Name:</span>
                                    <span class="font-medium text-right">{{ $order->customer_name ?? $order->user->name ?? 'Customer' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email:</span>
                                    <span class="font-medium text-right">{{ $order->customer_email ?? $order->user->email ?? 'Not provided' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Phone:</span>
                                    <span class="font-medium text-right">{{ $order->customer_phone ?? $order->phone ?? 'Not provided' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Address:</span>
                                    <span class="font-medium text-right max-w-xs">{{ $order->address ?? 'Not specified' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="mb-10">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Order Items</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="py-3 px-4 text-left text-gray-700 font-semibold text-sm uppercase tracking-wider border-b">Product</th>
                                    <th class="py-3 px-4 text-left text-gray-700 font-semibold text-sm uppercase tracking-wider border-b">Size</th>
                                    <th class="py-3 px-4 text-left text-gray-700 font-semibold text-sm uppercase tracking-wider border-b">Color</th>
                                    <th class="py-3 px-4 text-left text-gray-700 font-semibold text-sm uppercase tracking-wider border-b">Qty</th>
                                    <th class="py-3 px-4 text-left text-gray-700 font-semibold text-sm uppercase tracking-wider border-b">Price</th>
                                    <th class="py-3 px-4 text-left text-gray-700 font-semibold text-sm uppercase tracking-wider border-b">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $subtotal = 0;
                                    $orderItems = $order->items()->with('product')->get();
                                @endphp
                                
                                @foreach($orderItems as $item)
                                @php
                                    $itemTotal = $item->price * $item->quantity;
                                    $subtotal += $itemTotal;
                                @endphp
                                <tr class="border-b hover:bg-gray-50 transition-colors">
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3">
                                            @if($item->product && $item->product->main_image)
                                            <div class="flex-shrink-0">
                                                <img src="{{ asset('storage/' . $item->product->main_image) }}" 
                                                     alt="{{ $item->product->name }}" 
                                                     class="w-12 h-12 object-cover rounded-lg border border-gray-200">
                                            </div>
                                            @else
                                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200">
                                                <i class="fas fa-shoe-prints text-gray-400"></i>
                                            </div>
                                            @endif
                                            <div>
                                                <p class="font-medium text-gray-800">{{ $item->product->name ?? 'Product #' . $item->product_id }}</p>
                                                @if($item->product && $item->product->sku)
                                                <p class="text-xs text-gray-500 mt-1">SKU: {{ $item->product->sku }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-gray-700">{{ $item->size ?? '-' }}</td>
                                    <td class="py-4 px-4 text-gray-700">{{ $item->color ?? '-' }}</td>
                                    <td class="py-4 px-4 text-gray-700">{{ $item->quantity }}</td>
                                    <td class="py-4 px-4 text-gray-700">₱{{ number_format($item->price, 2) }}</td>
                                    <td class="py-4 px-4 font-semibold text-gray-900">₱{{ number_format($itemTotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Price Summary -->
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 mb-10">
                    <h2 class="text-lg font-semibold text-gray-800 mb-6">Price Summary</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between py-2">
                            <span class="text-gray-700">Items Subtotal</span>
                            <span class="font-semibold">₱{{ number_format($subtotal, 2) }}</span>
                        </div>
                        
                        @if($order->payment_method == 'cash_on_delivery' && $order->cod_fee > 0)
                        <div class="flex justify-between py-2 border-t border-gray-200 pt-3">
                            <span class="text-gray-700">COD Service Fee</span>
                            <span class="font-semibold">₱{{ number_format($order->cod_fee, 2) }}</span>
                        </div>
                        @endif
                        
                        @if($order->payment_method == 'paypal' && $order->shipping_fee > 0)
                        <div class="flex justify-between py-2 border-t border-gray-200 pt-3">
                            <span class="text-gray-700">Shipping Fee</span>
                            <span class="font-semibold">₱{{ number_format($order->shipping_fee, 2) }}</span>
                        </div>
                        @endif
                        
                        @if($order->discount > 0)
                        <div class="flex justify-between py-2 border-t border-gray-200 pt-3 text-green-600">
                            <span>Discount</span>
                            <span class="font-semibold">-₱{{ number_format($order->discount, 2) }}</span>
                        </div>
                        @endif
                        
                        <div class="flex justify-between py-3 border-t border-gray-300 pt-4 mt-2">
                            <span class="text-lg font-bold text-gray-900">Total Amount</span>
                            <span class="text-xl font-bold text-blue-600">₱{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="border-t border-gray-200 pt-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-3">Order Notes</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-600 text-sm">{{ $order->notes ?? 'No additional notes provided.' }}</p>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-3">Receipt Information</h3>
                            <div class="space-y-2 text-sm text-gray-600">
                                <p class="flex items-center gap-2">
                                    <i class="fas fa-calendar-check text-blue-500"></i>
                                    <span>Order Date: {{ $order->created_at->format('F d, Y') }}</span>
                                </p>
                                <p class="flex items-center gap-2">
                                    <i class="fas fa-file-invoice text-green-500"></i>
                                    <span>Receipt Generated: {{ now()->format('M d, Y') }}</span>
                                </p>
                                <p class="flex items-center gap-2">
                                    <i class="fas fa-user-tie text-gray-500"></i>
                                    <span>Generated by: {{ Auth::user()->name ?? 'Administrator' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Thank You -->
                    <div class="text-center py-6 border-t border-gray-200">
                        <div class="inline-flex items-center gap-3 text-gray-700 mb-3">
                            <i class="fas fa-heart text-red-400"></i>
                            <p class="font-semibold text-lg">Thank you for your order!</p>
                            <i class="fas fa-heart text-red-400"></i>
                        </div>
                        <p class="text-gray-500 text-sm">This is an official receipt. Please keep it for your records.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center no-print">
            <button onclick="window.print()" 
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold flex items-center justify-center gap-2 shadow-sm hover:shadow">
                <i class="fas fa-print"></i>
                Print Receipt
            </button>
            <a href="{{ route('admin.orders.show', $order->id) }}" 
               class="bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-900 transition font-semibold flex items-center justify-center gap-2 shadow-sm hover:shadow">
                <i class="fas fa-arrow-left"></i>
                Back to Order
            </a>
        </div>
    </div>

    <!-- Auto-print if print parameter exists -->
    <script>
        <?php if(request()->has('print')): ?>
            window.addEventListener('load', function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            });
        <?php endif; ?>
    </script>
</body>
</html>