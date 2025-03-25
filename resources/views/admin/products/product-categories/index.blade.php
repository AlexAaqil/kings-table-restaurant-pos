<x-authenticated-layout>
    <x-slot name="head">
        <title>Product Categories</title>
    </x-slot>

    <section class="Products">
        <div class="system_nav">
            <a href="{{ route('products.index') }}">Products</a>
            <span>Categories</span>
        </div>

        <div class="body row_container">
            <div class="column">
                @if ($product_categories->isNotEmpty())
                    <div class="table list_items">
                        <div class="header">
                            <div class="details">
                                <p class="title">Categories</p>
                                <p class="stats">
                                    <span>{{ $count_product_categories }} {{ Str::plural('Category', $count_product_categories) }}</span>
                                </p>
                            </div>
        
                            <x-search-input />
                        </div>
        
                        <table>
                            <thead>
                                <tr>
                                    <th class="center">#</th>
                                    <th>Category</th>
                                    <th>Slug</th>
                                    <th class="actions center">Actions</th>
                                </tr>
                            </thead>
                
                            <tbody>
                                @foreach ($product_categories as $category)
                                    <tr class="searchable">
                                        <td class="center">{{ $loop->iteration }}</td>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->slug }}</td>
                                        <td class="actions center">
                                            <div class="action_buttons">
                                                <div class="action">
                                                    <a href="{{ route('product-categories.edit', $category->id) }}">
                                                        <span class="fas fa-eye"></span> 
                                                    </a> 
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No product categories yet.</p>
                @endif
            </div>

            <div class="column">
                <div class="custom_form">
                    <div class="header">
                        <p>New Category</p>
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
            </div>
        </div>
    </section>

    <x-slot name="scripts">
        <x-search />
    </x-slot>
</x-authenticated-layout>
