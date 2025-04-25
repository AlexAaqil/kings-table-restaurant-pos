<x-authenticated-layout>
    <x-slot name="head">
        <title>Product | Update</title>
    </x-slot>

    <section class="Products">
        <div class="custom_form">
            <div class="header">
                <div class="icon">
                    <a href="{{ route('products.index') }}">
                        <span class="fas fa-arrow-left"></span>
                    </a>
                </div>
                <p>Update Product</p>
            </div>

            <form action="{{ route('products.update', $product->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('patch')

                <div class="input_group_3">
                    <div class="inputs">
                        <label for="category_id">Product Category</label>
                        <select name="category_id" id="category_id">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error field="category_id" />
                    </div>

                    <div class="inputs">
                        <label for="name" class="required">Name</label>
                        <input type="text" name="name" id="name" placeholder="Product Name" value="{{ old('name', $product->name) }}">
                        <x-input-error field="name" />
                    </div>

                    <div class="inputs">
                        <label for="product_code">Product Code</label>
                        <input type="number" name="product_code" id="product_code" placeholder="Product Code" value="{{ old('product_code', $product->product_code) }}">
                        <x-input-error field="product_code" />
                    </div>
                </div>

                <div class="input_group_3">
                    <div class="inputs">
                        <label for="buying_price">Buying Price</label>
                        <input type="number" step="0.01" name="buying_price" id="buying_price" value="{{ old('buying_price', $product->buying_price) }}" placeholder="Enter the Buying Price eg. 500.00" />
                        <x-input-error field="buying_price" />
                    </div>

                    <div class="inputs">
                        <label for="selling_price">Selling Price</label>
                        <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price', $product->selling_price) }}" placeholder="Enter the Selling Price eg. 700.00." />
                        <x-input-error field="selling_price" />
                    </div>

                    <div class="inputs">
                        <label for="discount_price">New Price (After Discount)</label>
                        <input type="number" step="0.01" name="discount_price" id="discount_price" value="{{ old('discount_price', $product->discount_price) }}" placeholder="Enter the discount_price." />
                        <x-input-error field="discount_price" />
                    </div>
                </div>

                <div class="input_group_3">
                    <div class="inputs">
                        <label for="is_visible">Is Visible?</label>
                        <div class="custom_radio_buttons">
                            <label>
                                <input class="option_radio" type="radio" name="is_visible" id="is_visible" value="1" {{ old('is_visible', $product->is_visible) == '1' ? 'checked' : '' }}>
                                <span>Yes</span>
                            </label>

                            <label>
                                <input class="option_radio" type="radio" name="is_visible" id="not_visible" value="0" {{ old('is_visible', $product->is_visible) == '0' ? 'checked' : '' }}>
                                <span>No</span>
                            </label>
                        </div>
                        <x-input-error field="is_visible" />
                    </div>

                    <div class="inputs">
                        <label for="stock_count">Stock Count</label>
                        <input type="number" name="stock_count" id="stock_count" placeholder="Stock in hand" value="{{ old('stock_count', $product->stock_count) }}" />
                        <x-input-error field="stock_count" />
                    </div>
    
                    <div class="inputs">
                        <label for="safety_stock">Safety Stock</label>
                        <input type="number" name="safety_stock" id="safety_stock" placeholder="Safety Stock" value="{{ old('safety_stock', $product->safety_stock) }}" />
                        <x-input-error field="safety_stock" />
                    </div>
                </div>                

                <div class="inputs">
                    <label for="images">Images ({{ $product->images->count() }} / 5 Maximum allowed images)</label>
                    <input type="file" name="images[]" id="images" accept="image/png, image/jpg, image/jpeg" multiple />
                    @if($product->images->count() >= 5) disabled @endif>
                    <span class="inline_alert">
                        @error('images.*') {{ $message }} @enderror
                        @error('images') {{ $message }} @enderror
                    </span>
                </div>

                @if(!empty(session('success')))
                    <span class="inline_alert_success">{{ session('success') }}</span>
                @endif

                <div class="product_images" id="sortable">
                    @if(!empty($product->images->count()))
                        @foreach ($product->images as $image)
                            @if(!empty($image->getProductImageURL()))
                                <div class="product_image sortable_images" id={{ $image->id }}>
                                    <img src="{{ $image->getProductImageURL() }}" alt="{{ $image->image_name }}" width="100" height="100">
                                    <a href="{{ route('products.delete_image', $image->id) }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>

                {{-- <div class="inputs">
                    <label for="description">Description</label>
                    <textarea name="description" id="editor_ckeditor" rows="10" placeholder="Enter a Description" class="tinymiced">{{ old('description', $product->description) }}</textarea>
                    <x-input-error field="description" />
                </div> --}}

                <div class="buttons">
                    <button type="submit">Update Product</button>

                    <button type="button" class="btn_danger" onclick="deleteItem({{ $product->id }}, 'product');"
                        form="deleteForm_{{ $product->id }}">
                        <i class="fas fa-trash-alt delete"></i>
                        <span>Delete Product</span>                        
                    </button>
                </div>
            </form>

            <form id="deleteForm_{{ $product->id }}" action="{{ route('products.destroy', $product->id) }}" method="post"
                style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </section>

    <x-slot name="scripts">
        <x-sweetalert />
        <script src="{{ asset('assets/js/jquery.js') }}"></script>
        <script src="{{ asset('assets/js/jquery_ui.js') }}"></script>
        {{-- <x-text-editor /> --}}
        <script>
            $(document).ready(function() {
            $("#sortable").sortable({
                update : function(event, ui) {
                    var photo_id = new Array();
                    $('.sortable_images').each(function() {
                        var id = $(this).attr('id');
                        photo_id.push(id);
                    });

                    $.ajax({
                        type : "POST",
                        url : "{{ url('products/images/sort') }}",
                        data : {
                            "photo_id" : photo_id,
                            "_token" : "{{ csrf_token() }}"
                        },
                        dataType : "json",
                        success : function(data) {

                        },
                        error : function (data) {

                        }
                    });
                }
            });
        });
        </script>
    </x-slot>
</x-authenticated-layout>
