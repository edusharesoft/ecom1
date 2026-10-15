<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Products - {{ $vendor->shop_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        @include('vendor.partials.sidebar')
        
        <main class="flex-1 overflow-y-auto ml-64 p-6">
        <h1 class="text-2xl font-bold mb-4">My Products</h1>
        <p class="text-gray-600 mb-6">Total: {{ $products->total() }} products</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse($products as $product)
                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="font-semibold">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $product->category->name ?? 'N/A' }}</p>
                    <p class="text-lg font-bold">৳{{ number_format($product->new_price ?? $product->old_price, 2) }}</p>
                </div>
            @empty
                <p>No products found.</p>
            @endforelse
        </div>
        
            {{ $products->links() }}
        </main>
    </div>
</body>
</html>
