<aside>
    @php
        $user = Auth::user();
        $user_level_label = $user->user_level_label;
    @endphp

    <div class="branding">
        <a href="{{ route('dashboard') }}">{{ config('globals.app_acronym') }}</a>
    </div>

    @php
        $nav_content = collect([
            [
                'route' => 'dashboard',
                'icon' => 'fas fa-home',
                'text' => 'Dashboard',
                'level' => [],
            ],
            [
                'route' => 'users.index',
                'icon' => 'fas fa-users',
                'text' => 'Users',
                'level' => ['super admin', 'admin'],
            ],
            [
                'route' => 'shop',
                'icon' => 'fas fa-barcode',
                'text' => 'Shop',
                'level' => ['cashier'],
            ],
            [
                'route' => 'cashier.sales',
                'icon' => 'fas fa-dollar-sign',
                'text' => 'Sales',
                'level' => ['cashier'],
            ],
            [
                'route' => 'sales.index',
                'icon' => 'fas fa-dollar-sign',
                'text' => 'Sales',
                'level' => ['super admin', 'admin'],
            ],
            [
                'route' => 'products.index',
                'icon' => 'fas fa-barcode',
                'text' => 'Products',
                'level' => ['super admin', 'admin'],
            ],
            // [
            //     'route' => 'messages.index',
            //     'icon' => 'fas fa-comment',
            //     'text' => 'Messages',
            //     'level' => ['super admin', 'admin'],
            // ],
        ]);

        $nav_links = $nav_content->filter(function($link) use($user_level_label) {
            return empty($link['level']) || in_array($user_level_label, $link['level']);
        });
    @endphp

    <ul class="links">
        @foreach ($nav_links as $link)
            <li class="link {{ Route::currentRouteName() === $link['route'] ? 'active' : '' }}">
                <a href="{{ route($link['route']) }}">
                    <i class="{{ $link['icon'] }}"></i>
                    <span class="text">{{ $link['text'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    <div class="footer">
        <div class="profile">
            <a href="{{ route('profile.edit') }}">
                @if($user->image)
                    <img src="{{ asset('storage/' . ($user->image)) }}" alt="User Image" width="25" height="25">
                @else
                    <x-default-profile-image width="25" height="25" />
                @endif
            </a>
            <span class="text">
                {{ strtoupper(substr($user->first_name, 0, 1)) . ' . ' . strtoupper(substr($user->last_name, 0, 1)) }}
            </span>
        </div>

        <div class="logout">
            <form action="{{ route('logout') }}" method="post">
                @csrf

                <button type="submit">
                    <span class="text">Logout</span>
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
