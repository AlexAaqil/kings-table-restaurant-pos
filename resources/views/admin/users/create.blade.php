<x-authenticated-layout>
    <x-slot name="head">
        <title>User | New</title>
    </x-slot>

    <section class="Users">
        <div class="custom_form">
            <div class="header">
                <div class="icon">
                    <a href="{{ route('users.index') }}">
                        <span class="fas fa-arrow-left"></span>
                    </a>
                </div>
                <p>New User</p>
            </div>

            <form action="{{ route('users.store') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="input_group">
                    <div class="inputs">
                        <label for="user_level">User Level</label>
                        <div class="custom_radio_buttons">
                            @foreach(App\Models\User::USERLEVELS as $key => $label)
                                <label>
                                    <input class="option_radio" 
                                        type="radio" 
                                        name="user_level" 
                                        value="{{ $key }}"
                                        {{ old('user_level', 1) == $key ? 'checked' : '' }}>
                                    <span>{{ ucfirst($label) }}</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error field="user_level" />
                    </div>

                    <div class="inputs">
                        <label for="user_status">User Status</label>
                        <div class="custom_radio_buttons">
                            @foreach(App\Models\User::USERSTATUS as $key => $label)
                                <label>
                                    <input class="option_radio" 
                                        type="radio" 
                                        name="user_status" 
                                        value="{{ $key }}"
                                        {{ old('user_status', 1) == $key ? 'checked' : '' }}>
                                    <span>{{ ucfirst($label) }}</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error field="user_status" />
                    </div>
                </div>

                <div class="input_group">
                    <div class="inputs">
                        <label for="first_name" class="required">First Name</label>
                        <input type="text" name="first_name" id="first_name" placeholder="First Name" value="{{ old('first_name') }}">
                        <x-input-error field="first_name" />
                    </div>
    
                    <div class="inputs">
                        <label for="last_name" class="required">Last Name</label>
                        <input type="text" name="last_name" id="last_name" placeholder="Last Name" value="{{ old('last_name') }}">
                        <x-input-error field="last_name" />
                    </div>
                </div>
    
                <div class="input_group">
                    <div class="inputs">
                        <label for="email" class="required">Email Address</label>
                        <input type="email" name="email" id="email" placeholder="Email Address" value="{{ old('email') }}">
                        <x-input-error field="email" />
                    </div>
    
                    <div class="inputs">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" name="phone_number" id="phone_number" placeholder="Phone Number" value="{{ old('phone_number') }}">
                        <x-input-error field="phone_number" />
                    </div>
                </div>
    
                {{-- <div class="input_group">
                    <div class="inputs">
                        <label for="password" class="required">Password</label>
                        <input type="password" name="password" id="password" placeholder="Password" value="{{ old('password') }}">
                        <x-input-error field="password" />
                    </div>
        
                    <div class="inputs">
                        <label for="password_confirmation" class="required">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" value="{{ old('password_confirmation') }}">
                        <x-input-error field="password_confirmation" />
                    </div>
                </div> --}}

                <button type="submit">Save User</button>
            </form>
        </div>
    </section>

    <x-slot name="scripts">
        <x-text-editor />
    </x-slot>
</x-authenticated-layout>
