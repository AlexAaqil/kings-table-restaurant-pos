<x-authenticated-layout>
    <x-slot name="head">
        <title>Product | New</title>
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

            <form action="{{ route('products.store') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="input_group_3">
                    <div class="inputs">
                        <label for="category_id">Product Category</label>
                        <select name="category_id">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ $category->id == $selected_category_id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error field="category_id" />
                    </div>

                    <div class="inputs">
                        <label for="name" class="required">Name</label>
                        <input type="text" name="name" id="name" placeholder="Product Name" value="{{ old('name') }}">
                        <x-input-error field="name" />
                    </div>

                    <div class="inputs">
                        <label for="product_code">Product Code</label>
                        <input type="number" name="product_code" id="product_code" placeholder="Product Code" value="{{ old('product_code', 0) }}">
                        <x-input-error field="product_code" />
                    </div>
                </div>

                <div class="input_group_3">
                    <div class="inputs">
                        <label for="buying_price">Buying Price</label>
                        <input type="number" step="0.01" name="buying_price" id="buying_price" value="{{ old('buying_price', 0.00) }}" placeholder="Enter the Buying Price eg. 500.00" />
                        <x-input-error field="buying_price" />
                    </div>

                    <div class="inputs">
                        <label for="selling_price">Selling Price</label>
                        <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price', 0.00) }}" placeholder="Enter the Selling Price eg. 700.00." />
                        <x-input-error field="selling_price" />
                    </div>

                    <div class="inputs">
                        <label for="discount_price">New Price (After Discount)</label>
                        <input type="number" step="0.01" name="discount_price" id="discount_price" value="{{ old('discount_price', 0.00) }}" placeholder="Enter the discount_price." />
                        <x-input-error field="discount_price" />
                    </div>
                </div>

                <div class="input_group_3">
                    <div class="inputs">
                        <label for="is_visible">Is Visible?</label>
                        <div class="custom_radio_buttons">
                            <label>
                                <input class="option_radio" type="radio" name="is_visible" id="is_visible" value="1" {{ old('is_visible', 1) == '1' ? 'checked' : '' }}>
                                <span>Yes</span>
                            </label>

                            <label>
                                <input class="option_radio" type="radio" name="is_visible" id="not_visible" value="0" {{ old('is_visible') == '0' ? 'checked' : '' }}>
                                <span>No</span>
                            </label>
                        </div>
                        <x-input-error field="is_visible" />
                    </div>

                    <div class="inputs">
                        <label for="stock_count">Stock Count</label>
                        <input type="number" name="stock_count" id="stock_count" placeholder="Stock in hand" value="{{ old('stock_count', 1) }}" />
                        <x-input-error field="stock_count" />
                    </div>
    
                    <div class="inputs">
                        <label for="safety_stock">Safety Stock</label>
                        <input type="number" name="safety_stock" id="safety_stock" placeholder="Safety Stock" value="{{ old('safety_stock', 1) }}" />
                        <x-input-error field="safety_stock" />
                    </div>
                </div>

                <div class="inputs">
                    <label for="images">Images (Maximum allowed images is 5)</label>
                    <input type="file" name="images[]" id="images" accept=".png, .jpg, .jpeg" multiple />
                    <x-input-error field="images" />
                </div>

                {{-- <div class="inputs">
                    <label for="description">Description</label>
                    <textarea name="description" id="editor_ckeditor" rows="10" placeholder="Enter a Description" class="tinymiced">{{ old('description') }}</textarea>
                    <x-input-error field="description" />
                </div> --}}

                <button type="submit">Save Product</button>
            </form>
        </div>
    </section>

    <x-slot name="scripts">
        {{-- <x-text-editor /> --}}
    </x-slot>
</x-authenticated-layout>
