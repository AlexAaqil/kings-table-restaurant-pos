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
            @if ($categories->isNotEmpty())
                <div class="table list_items">
                    <div class="header">
                        <div class="details">
                            <p class="title">Products</p>
                            <p class="stats">
                                <span>{{ $categories->sum(fn($category) => $category->products->count()) }} Products</span>
                            </p>
                        </div>
    
                        <x-search-input />

                        <div class="btn">
                            <a href="{{ route('product-categories.create') }}">New Category</a>
                        </div>
                    </div>

                    @foreach($categories as $category)
                        <div class="categorized_products">
                            <div class="category_header">
                                <p class="title">{{ $category->name }}</p>

                                <div class="text">
                                    <p>{{ $category->products->count() }} Products</p>
                                    <a href="{{ route('products.create', ['category_id' => $category->id]) }}" class="add-product-btn">Add Product</a>
                                </div>
                            </div>

                            <div class="cards">
                                @forelse($category->products as $product)
                                    <div class="card searchable {{ $product->is_visible ? '' : 'danger_border' }}">
                                        <div class="image">
                                            <a href="{{ route('products.edit', $product->id) }}">
                                                <img src="{{ $product->getFirstImage() ?? asset('assets/images/default_image.jpg') }}" alt="Product Image" width="300" height="300">
                                            </a>
                                        </div>

                                        <div class="text">
                                            <div class="extra_details">
                                                @if($product->is_visible == 1)
                                                    <span class="success">available</span>
                                                @else
                                                    <span class="danger">unavailable</span>
                                                @endif
                                            </div>

                                            <div class="details">
                                                <a href="{{ route('products.edit', $product->id) }}">
                                                    <p class="title">{{ $product->name }}</p>
                                                </a>

                                                <p class="prices">
                                                    @if($product->discount_price && $product->discount_price < $product->selling_price)
                                                        <span class="price success">Ksh. {{ number_format($product->getEffectivePrice(), 2) }}</span>
                                                        <span>
                                                            <span class="old_price danger"><del>{{ number_format($product->selling_price, 2) }}</del></span>
                                                            <span class="discount">({{ $product->calculateDiscount() }}% off)</span>
                                                        </span>
                                                    @else
                                                        <span class="price success">Ksh. {{ number_format($product->getEffectivePrice(), 2) }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="no-products">No products in this category.</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p>No categories yet.</p>
                <a href="{{ route('product-categories.create') }}">Add New Category</a>
            @endif
        </div>
    </section>

    <x-slot name="scripts">
        <x-search />
    </x-slot>
</x-authenticated-layout>
