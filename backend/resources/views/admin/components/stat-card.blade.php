<div class="admin-card p-6">
    <div class="flex items-center">
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-600">{{ $title }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $value }}</p>
            @isset($subtitle)
                <p class="text-sm text-gray-500 mt-1">{{ $subtitle }}</p>
            @endisset
        </div>
        @isset($icon)
            <div class="ml-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    {!! $icon !!}
                </div>
            </div>
        @endisset
    </div>
</div>
