@extends('admin.layouts.admin')

@section('title', __('admin.reservations'))
@section('page-title', __('admin.reservations_management'))

@section('header-actions')
<div class="flex space-x-3">
    <form method="GET" class="flex space-x-2">
        <input type="text"
               name="search"
               value="{{ request('search') }}"
               placeholder="{{ __('admin.search_reservations') }}"
               class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">

        <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">{{ __('admin.all_statuses') }}</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('admin.pending') }}</option>
            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>{{ __('admin.confirmed') }}</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('admin.cancelled') }}</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('admin.completed') }}</option>
        </select>

        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            {{ __('admin.search') }}
        </button>
    </form>
</div>
@endsection

@section('content')
<div class="admin-card">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('admin.reservation_id') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('admin.property') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('admin.guest') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('admin.dates') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('admin.status') }}
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('admin.actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reservations as $reservation)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        #{{ $reservation->id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $reservation->property->name ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-500">{{ $reservation->property->location ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $reservation->user->name ?? $reservation->user->username ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-500">{{ $reservation->user->email ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ $reservation->checkin_date ? $reservation->checkin_date->format('d/m/Y') : 'N/A' }}
                            -
                            {{ $reservation->checkout_date ? $reservation->checkout_date->format('d/m/Y') : 'N/A' }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $reservation->duration_in_nights ? $reservation->duration_in_nights . ' ' . __('admin.nights') : '' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            @if($reservation->status === 'confirmed') bg-green-100 text-green-800
                            @elseif($reservation->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($reservation->status === 'cancelled') bg-red-100 text-red-800
                            @elseif($reservation->status === 'completed') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ __('admin.' . ($reservation->status ?? 'unknown')) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="#" class="text-blue-600 hover:text-blue-900 transition-colors">
                            {{ __('admin.view') }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        {{ __('admin.no_reservations_found') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reservations->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $reservations->links() }}
    </div>
    @endif
</div>
@endsection
