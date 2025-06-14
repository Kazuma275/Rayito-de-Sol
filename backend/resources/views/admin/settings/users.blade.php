@extends('admin.layouts.admin')

@section('title', __('admin.user_management'))
@section('page-title', __('admin.user_management'))

@section('header-actions')
<div class="flex space-x-3">
    <form method="GET" class="flex space-x-2">
        <input type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="{{ __('admin.search_users') }}"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">

        <select name="role" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">{{ __('admin.all_roles') }}</option>
            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>{{ __('admin.user') }}</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>{{ __('admin.admin') }}</option>
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
                        {{ __('admin.user') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('admin.role') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('admin.status') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('admin.registered') }}
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('admin.actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-sm font-medium text-blue-600">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $user->hasRole('admin') ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $user->hasRole('admin') ? __('admin.admin') : __('admin.user') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->is_active ? __('admin.active') : __('admin.inactive') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-2">
                            <button onclick="editUser({{ $user->id }}, '{{ $user->name ?? '' }}', '{{ $user->email }}', '{{ $user->username }}', {{ $user->verificado ? 'true' : 'false' }}, '{{ $user->role }}')"
                                class="text-blue-600 hover:text-blue-900 transition-colors">
                                {{ __('admin.edit') }}
                            </button>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.settings.users.delete', $user) }}"
                                onsubmit="return confirm('{{ __('admin.confirm_delete_user') }}')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors">
                                    {{ __('admin.delete') }}
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        {{ __('admin.no_users_found') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $users->links() }}
    </div>
    @endif
</div>

<!-- Modal para editar usuario -->
<div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">{{ __('admin.edit_user') }}</h3>
        </div>

        <form id="editUserForm" method="POST">
            @csrf
            @method('PUT')

            <div class="px-6 py-4 space-y-4">
                <div>
                    <label for="edit_username" class="block text-sm font-medium text-gray-700">{{ __('admin.username') }}</label>
                    <input type="text" id="edit_username" name="username" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="edit_name" class="block text-sm font-medium text-gray-700">{{ __('admin.name') }}</label>
                    <input type="text" id="edit_name" name="name" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="edit_email" class="block text-sm font-medium text-gray-700">{{ __('admin.email') }}</label>
                    <input type="email" id="edit_email" name="email" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="edit_role" class="block text-sm font-medium text-gray-700">{{ __('admin.role') }}</label>
                    <select id="edit_role" name="role" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="user">{{ __('admin.user') }}</option>
                        <option value="admin">{{ __('admin.admin') }}</option>
                    </select>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="edit_is_active" name="is_active" value="1"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="edit_is_active" class="ml-2 block text-sm text-gray-900">
                        {{ __('admin.active_user') }}
                    </label>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                <button type="button" onclick="closeEditModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                    {{ __('admin.cancel') }}
                </button>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 transition-colors">
                    {{ __('admin.save_changes') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editUser(id, name, email, username, verificado, role) {
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_username').value = username; // Esta línea ya está
    document.getElementById('edit_role').value = role;
    document.getElementById('edit_is_active').checked = verificado;
    document.getElementById('editUserForm').action = `/admin/settings/users/${id}`;
    document.getElementById('editUserModal').classList.remove('hidden');
    document.getElementById('editUserModal').classList.add('flex');
}
</script>
<script>
function closeEditModal() {
document.getElementById('editUserModal').classList.add('hidden');
document.getElementById('editUserModal').classList.remove('flex');
}

// Cerrar modal al hacer clic fuera
document.getElementById('editUserModal').addEventListener('click', function(e) {
if (e.target === this) {
closeEditModal();
}
});
</script>
@endsection
