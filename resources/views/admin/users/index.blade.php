<x-authenticated-layout>
    <x-slot name="head">
        <title>Users</title>
    </x-slot>

    <section class="Users">
        <div class="body">
            @if ($users->isNotEmpty())
                <div class="table">
                    <div class="header">
                        <div class="details">
                            <p class="title">Users</p>
                            <p class="stats">
                                <span>{{ $count_users }} {{ Str::plural('User', $count_users) }}</span>
                                <span>{{ $count_admins }} {{ Str::plural('Admin', $count_admins) }}</span>
                                <span>{{ $count_inactive }} Inactive</span>
                            </p>
                        </div>
    
                        <x-search-input />

                        <div class="btn">
                            <a href="{{ route('users.create') }}">New User</a>
                        </div>
                    </div>
    
                    <table>
                        <thead>
                            <tr>
                                <th class="center">#</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th class="actions center">Actions</th>
                            </tr>
                        </thead>
            
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="searchable {{ $user->user_status == 0 ? 'inactive' : '' }}">
                                    <td class="center">{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($user->user_level_label === 'admin' || $user->user_level_label === 'super admin')
                                            {{ $user->full_name }}
                                            <span class="badge {{ $user->user_level_label === 'admin' ? 'admin' : 'super_admin' }}">
                                                {{ $user->user_level_label }}
                                            </span>
                                        @else
                                            {{ $user->full_name }}
                                        @endif
                                    </td>
                                    <td class="stack {{ $user->email_verified_at == null ? 'unverified' : '' }}">
                                        <span>{{ $user->email }}</span>
                                        <span>{{ $user->phone_numbers }}</span>
                                    </td>
                                    <td class="actions center">
                                        <div class="action">
                                            <a href="{{ route('users.edit', $user->id) }}">
                                                <span class="fas fa-eye"></span> 
                                            </a> 
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>No users yet.</p>
            @endif
        </div>
    </section>

    <x-slot name="scripts">
        <x-search />
    </x-slot>
</x-authenticated-layout>
