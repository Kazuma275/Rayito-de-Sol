@extends('admin.layouts.admin')

@section('title', __('admin.system_settings'))
@section('page-title', __('admin.system_settings'))

@section('content')
<div class="space-y-6">
    <!-- Información del sistema -->
    <div class="admin-card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('admin.system_information') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('admin.app_name') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $settings['app_name'] }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('admin.app_url') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $settings['app_url'] }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('admin.mail_driver') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $settings['mail_driver'] }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('admin.cache_driver') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $settings['cache_driver'] }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('admin.session_driver') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $settings['session_driver'] }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('admin.laravel_version') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ app()->version() }}</dd>
            </div>
        </div>
    </div>

    <!-- Herramientas de mantenimiento -->
    <div class="admin-card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('admin.maintenance_tools') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <button class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <div class="text-left">
                    <h4 class="font-medium text-gray-900">{{ __('admin.clear_cache') }}</h4>
                    <p class="text-sm text-gray-600">{{ __('admin.clear_application_cache') }}</p>
                </div>
            </button>

            <button class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <div class="text-left">
                    <h4 class="font-medium text-gray-900">{{ __('admin.view_logs') }}</h4>
                    <p class="text-sm text-gray-600">{{ __('admin.check_application_logs') }}</p>
                </div>
            </button>

            <button class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                <svg class="w-8 h-8 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div class="text-left">
                    <h4 class="font-medium text-gray-900">{{ __('admin.maintenance_mode') }}</h4>
                    <p class="text-sm text-gray-600">{{ __('admin.enable_maintenance_mode') }}</p>
                </div>
            </button>
        </div>
    </div>
</div>
@endsection
