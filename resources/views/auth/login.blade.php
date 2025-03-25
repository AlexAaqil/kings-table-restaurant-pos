<x-guest-layout class="Authentication">
    <x-slot name="head">
        <title>Login | {{ config('globals.app_name') }}</title>
    </x-slot>

    <section class="Login">
        <div class="container">
            <form action="{{ route('login') }}" method="post">
                @csrf
                
                <div class="header">
                    <p>Login</p>
                </div>
                
                <div class="inputs">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" placeholder="Email Address" value="{{ old('email') }}" autofocus>
                    <x-input-error field="email" />
                </div>
    
                <div class="inputs">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Password"
                        value="{{ old('password') }}">
                </div>
    
                <p>
                    <a href="{{ route('password.request') }}">Forgot your password?</a>
                </p>
    
                <button type="submit">Login</button>
            </form>
        </div>
    </section>
</x-guest-layout>
