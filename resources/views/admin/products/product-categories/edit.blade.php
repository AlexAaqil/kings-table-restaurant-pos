<x-authenticated-layout>
    <x-slot name="head">
        <title>Product Category | Update</title>
    </x-slot>

    <section class="Products">
        <div class="custom_form">
            <div class="header">
                <div class="icon">
                    <a href="{{ route('product-categories.index') }}">
                        <span class="fas fa-arrow-left"></span>
                    </a>
                </div>
                <p>Update Category</p>
            </div>

            <form action="{{ route('product-categories.update', $product_category->id) }}" method="post">
                @csrf
                @method('patch')

                <div class="inputs">
                    <label for="name">Category Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product_category->name) }}">
                    <x-input-error field="name" />
                </div>

                <div class="buttons">
                    <button type="submit">Update Category</button>

                    <button type="button" class="btn_danger" onclick="deleteItem({{ $product_category->id }}, 'category and its products');"
                        form="deleteForm_{{ $product_category->id }}">
                        <i class="fas fa-trash-alt delete"></i>
                        <span>Delete Category</span>                        
                    </button>
                </div>
            </form>

            <form id="deleteForm_{{ $product_category->id }}" action="{{ route('product-categories.destroy', $product_category->id) }}" method="post"
                style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </section>

    <x-slot name="scripts">
        <x-sweetalert />
    </x-slot>
</x-authenticated-layout>
