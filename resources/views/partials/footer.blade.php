<footer>
    <div class="container">
        <section class="branding">
            <p class="title">{{ $appSettings['school_name'] ?? config('globals.app_name') }}</p>
            <p>Better start for new projects.</p>
            <p>{{ $appSettings['app_address'] ?? config('globals.app_address') }}</p>
        </section>

        <section class="links">
            <a href="{{ Route::has('home-page') ? route('home-page') : '#' }}">Home</a>
            <a href="{{ Route::has('about-page') ? route('about-page') : '#' }}">About</a>
            <a href="{{ Route::has('services-page') ? route('about-page') : '#' }}">Services</a>
            <a href="{{ Route::has('contact-page') ? route('contact-page') : '#' }}">Contact</a>
        </section>

        <section class="contacts">
            <div class="details">
                <p>
                    {{ $appSettings['app_phone_number'] ?? config('globals.app_phone_number') }}
                    @if(!empty($appSettings['app_phone_other']))
                        / {{ $appSettings['app_phone_other'] }}
                    @endif
                    @if(!empty(config('globals.app_phone_other')))
                        / {{ config('globals.app_phone_other') }}
                    @endif
                </p>
                <p>{{ $appSettings['app_email'] ?? config('globals.app_email') }}</p>
            </div>

            <div class="socials">
                <a href="https://wa.me/{{ $appSettings['whatsapp_number'] ?? config('globals.app_whatsapp_number') }}">
                    <img src="{{ asset('assets/images/whatsapp.png') }}" alt="{{ config('globals.app_name') }} Whatsapp" width="30px" height="30px">
                </a>

                <a href="#">
                    <img src="{{ asset('assets/images/instagram.png') }}" alt="{{ config('globals.app_name') }} Instagram" width="30px" height="30px">
                </a>
            </div>
        </section>
    </div>

    <p class="copyright">&copy; 2024 | All rights reserved</p>
</footer>