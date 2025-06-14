<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __('admin.admin_panel')) - {{ config('app.name') }}</title>

    {{-- Reemplazar Vite con CDN --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .admin-gradient {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        }
        .admin-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.1);
        }
        .admin-sidebar {
            background: linear-gradient(180deg, #1e40af 0%, #1e3a8a 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="admin-sidebar w-64 text-white">
            <div class="p-6">
                <h2 class="text-xl font-bold">{{ __('admin.admin_panel') }}</h2>
                <p class="text-blue-200 text-sm mt-1">{{ auth()->user()->name ?? auth()->user()->username }}</p>
            </div>

            <nav class="mt-6">
                @component('admin.components.nav-item', [
                    'route' => 'admin.settings.index',
                    'icon' => 'dashboard',
                    'active' => request()->routeIs('admin.settings.index')
                ])
                    {{ __('admin.dashboard') }}
                @endcomponent

                @component('admin.components.nav-item', [
                    'route' => 'admin.settings.users',
                    'icon' => 'users',
                    'active' => request()->routeIs('admin.settings.users*')
                ])
                    {{ __('admin.user_management') }}
                @endcomponent

                @component('admin.components.nav-item', [
                    'route' => 'admin.settings.properties',
                    'icon' => 'properties',
                    'active' => request()->routeIs('admin.settings.properties*')
                ])
                    {{ __('admin.properties') }}
                @endcomponent

                @component('admin.components.nav-item', [
                    'route' => 'admin.settings.reservations',
                    'icon' => 'reservations',
                    'active' => request()->routeIs('admin.settings.reservations*')
                ])
                    {{ __('admin.reservations') }}
                @endcomponent

                @component('admin.components.nav-item', [
                    'route' => 'admin.settings.system',
                    'icon' => 'settings',
                    'active' => request()->routeIs('admin.settings.system')
                ])
                    {{ __('admin.system_settings') }}
                @endcomponent
            </nav>

            <div class="absolute bottom-0 w-64 p-6">
                <a href="{{ url('/') }}" class="flex items-center text-blue-200 hover:text-white transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('admin.back_to_app') }}
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-gray-900">
                            @yield('page-title', __('admin.admin_panel'))
                        </h1>

                        <div class="flex items-center space-x-4">
                            @hasSection('header-actions')
                                @yield('header-actions')
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-gray-500 hover:text-gray-700 transition-colors">
                                    {{ __('admin.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 p-6">
                @if(session('success'))
                    @component('admin.components.alert', ['type' => 'success'])
                        {{ session('success') }}
                    @endcomponent
                @endif

                @if(session('error'))
                    @component('admin.components.alert', ['type' => 'error'])
                        {{ session('error') }}
                    @endcomponent
                @endif

                @if($errors->any())
                    @component('admin.components.alert', ['type' => 'error'])
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endcomponent
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
