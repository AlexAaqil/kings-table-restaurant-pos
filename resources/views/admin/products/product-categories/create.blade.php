<x-authenticated-layout>
    <x-slot name="head">
        <title>Product Category | New</title>
    </x-slot>

    <section class="Products">
        <div class="custom_form">
            <div class="header">
                <div class="icon">
                    <a href="{{ route('products.index') }}">
                        <span class="fas fa-arrow-left"></span>
                    </a>
                </div>
                
                <p>New Product</p>
            </div>

            <form action="{{ route('product-categories.store') }}" method="post">
                @csrf

                <div class="inputs">
                    <label for="name">Category Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}">
                    <x-input-error field="name" />
                </div>

                <button type="submit">Save</button>
            </form>
        </div>
    </section>

    <x-slot name="scripts">
        {{-- <x-text-editor /> --}}
    </x-slot>
</x-authenticated-layout>
