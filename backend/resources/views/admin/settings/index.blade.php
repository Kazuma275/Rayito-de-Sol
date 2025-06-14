@extends('admin.layouts.admin')

@section('title', __('admin.dashboard'))
@section('page-title', __('admin.dashboard'))

@section('content')
<div class="space-y-6">
    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @component('admin.components.stat-card', [
            'title' => __('admin.total_users'),
            'value' => number_format($stats['total_users']),
            'subtitle' => __('admin.registered_users'),
            'icon' => '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>'
        ])
        @endcomponent

        @component('admin.components.stat-card', [
            'title' => __('admin.active_users'),
            'value' => number_format($stats['active_users']),
            'subtitle' => __('admin.verified_users'),
            'icon' => '<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        ])
        @endcomponent

        @component('admin.components.stat-card', [
            'title' => __('admin.administrators'),
            'value' => number_format($stats['admin_users']),
            'subtitle' => __('admin.admin_accounts'),
            'icon' => '<svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>'
        ])
        @endcomponent

        @component('admin.components.stat-card', [
            'title' => __('admin.recent_registrations'),
            'value' => number_format($stats['recent_registrations']),
            'subtitle' => __('admin.last_7_days'),
            'icon' => '<svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>'
        ])
        @endcomponent

        @component('admin.components.stat-card', [
            'title' => __('admin.total_properties'),
            'value' => number_format($stats['total_properties']),
            'subtitle' => __('admin.listed_properties'),
            'icon' => '<svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>'
        ])
        @endcomponent

        @component('admin.components.stat-card', [
            'title' => __('admin.total_reservations'),
            'value' => number_format($stats['total_reservations']),
            'subtitle' => __('admin.all_reservations'),
            'icon' => '<svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>'
        ])
        @endcomponent
    </div>

    <!-- Acciones rápidas -->
    <div class="admin-card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('admin.quick_actions') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.settings.users') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                <div>
                    <h4 class="font-medium text-gray-900">{{ __('admin.manage_users') }}</h4>
                    <p class="text-sm text-gray-600">{{ __('admin.view_edit_users') }}</p>
                </div>
            </a>

            <a href="{{ route('admin.settings.properties') }}" class="flex items-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                <svg class="w-8 h-8 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <div>
                    <h4 class="font-medium text-gray-900">{{ __('admin.manage_properties') }}</h4>
                    <p class="text-sm text-gray-600">{{ __('admin.view_properties') }}</p>
                </div>
            </a>

            <a href="{{ route('admin.settings.reservations') }}" class="flex items-center p-4 bg-teal-50 rounded-lg hover:bg-teal-100 transition-colors">
                <svg class="w-8 h-8 text-teal-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <div>
                    <h4 class="font-medium text-gray-900">{{ __('admin.manage_reservations') }}</h4>
                    <p class="text-sm text-gray-600">{{ __('admin.view_reservations') }}</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
