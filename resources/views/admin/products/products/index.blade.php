<x-authenticated-layout>
    <x-slot name="head">
        <title>Products</title>
    </x-slot>

    <section class="Products">
        <div class="system_nav">
            <a href="{{ route('product-categories.index') }}">Categories</a>
            <span>Products</span>
        </div>
        
        <div class="body">
            @if ($products->isNotEmpty())
                <div class="table list_items">
                    <div class="header">
                        <div class="details">
                            <p class="title">Products</p>
                            <p class="stats">
                                <span>{{ $count_products }} {{ Str::plural('Product', $count_products) }}</span>
                            </p>
                        </div>
    
                        <x-search-input />

                        <div class="btn">
                            <a href="{{ route('products.create') }}">New Product</a>
                        </div>
                    </div>
    
                    <div class="cards">
                        @foreach($products as $product)
                            <div class="card searchable">
                                <div class="image">
                                    <a href="{{ route('products.edit', $product->id) }}">
                                        <img src="{{ $product->getFirstImage() ?? asset('assets/images/default_image.jpg') }}" alt="Product Image" width="300" height="300">
                                    </a>
                                </div>

                                <div class="text">
                                    <div class="extra_details">

                                    </div>

                                    <div class="details">
                                        <a href="{{ route('products.edit', $product->id) }}">
                                            <p class="title">{{ $product->name }}</p>
                                        </a>

                                        <p class="prices">
                                            @if($product->discount_price && $product->discount_price < $product->selling_price)
                                                <span class="price success">Ksh. {{ number_format($product->getEffectivePrice(), 2) }}</span>
                                                <span class="old_price danger"><del>{{ number_format($product->selling_price, 2) }}</del></span>
                                                <span class="discount">({{ $product->calculateDiscount() }}% off)</span>
                                            @else
                                                <span class="price success">Ksh. {{ number_format($product->getEffectivePrice(), 2) }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <p>No products yet.</p>
                <a href="{{ route('products.create') }}">Add New</a>
            @endif
        </div>
    </section>

    <x-slot name="scripts">
        <x-search />
    </x-slot>
</x-authenticated-layout>
