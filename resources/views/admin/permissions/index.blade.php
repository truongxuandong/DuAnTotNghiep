@extends('admin.layouts.admin')

@section('title','Permissions Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Permissions Management</h1>
        <div class="flex gap-3">
            <a 
                href="{{ route('admin.permissions.roles') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
            >
                <i class="fa-solid fa-user-tag mr-2"></i>
                Manage Roles
            </a>
            <a 
                href="{{ route('admin.permissions.permissions') }}"
                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium"
            >
                <i class="fa-solid fa-key mr-2"></i>
                Manage Permissions
            </a>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px" aria-label="Tabs">
                <button onclick="showTab('users')" id="tab-users" class="tab-button active px-6 py-4 text-sm font-medium border-b-2 border-blue-500 text-blue-600">
                    Users & Roles
                </button>
                <button onclick="showTab('permissions')" id="tab-permissions" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Role Permissions
                </button>
            </nav>
        </div>

        <!-- Users & Roles Tab -->
        <div id="content-users" class="tab-content p-6">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Users and Their Roles</h2>
                @php
                    $columns = [
                        ['label' => '#', 'key' => '__index', 'class' => 'w-12', 'td_class' => 'font-medium'],
                        ['label' => 'Name', 'key' => 'name'],
                        ['label' => 'Email', 'key' => 'email'],
                        ['label' => 'Roles', 'key' => 'roles', 'type' => 'roles-list'],
                        ['label' => 'Status', 'key' => 'status', 'type' => 'status-boolean'],
                    ];

                    $actions = [
                        [
                            'label' => 'Edit Permissions',
                            'icon' => 'fa-key',
                            'onclick' => 'openUserPermissionsDialog({id})',
                            'class' => 'text-purple-600 hover:text-purple-800',
                        ],
                    ];
                @endphp
                <x-common-table :columns="$columns" :rows="$users" :showPagination="true" :actions="$actions" />
            </div>
        </div>

        <!-- Role Permissions Tab -->
        <div id="content-permissions" class="tab-content p-6 hidden">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Role Permissions Matrix</h2>
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200" id="permissionsTreeTable">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64">
                                Module / Action
                            </th>
                            @foreach($roles as $role)
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[100px]">
                                    {{ $role->name }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($modules as $module => $actions)
                            @php
                                $moduleId = 'module-' . $module;
                            @endphp
                            {{-- Module Row (Parent) --}}
                            <tr 
                                class="module-row bg-gray-50 hover:bg-gray-100"
                                data-module="{{ $module }}"
                                id="row-{{ $moduleId }}"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <button 
                                            type="button"
                                            onclick="toggleModule('{{ $module }}')"
                                            class="module-toggle w-5 h-5 mr-3 shrink-0 text-gray-600 hover:text-gray-800 transition-transform cursor-pointer"
                                            data-module="{{ $module }}"
                                            data-expanded="false"
                                            title="Click to expand/collapse"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                        <span class="font-semibold text-gray-900">{{ ucfirst($module) }}</span>
                                    </div>
                                </td>
                                @foreach($roles as $role)
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                        @php
                                            $allActionsHavePermission = true;
                                            foreach($actions as $action) {
                                                $permissionName = $module . '.' . $action;
                                                if (!$role->permissions->contains('name', $permissionName)) {
                                                    $allActionsHavePermission = false;
                                                    break;
                                                }
                                            }
                                        @endphp
                                        <button
                                            onclick="toggleModulePermission({{ $role->id }}, '{{ $module }}', this)"
                                            class="module-permission-toggle w-6 h-6 rounded border-2 transition {{ $allActionsHavePermission ? 'bg-green-500 border-green-600' : 'bg-gray-200 border-gray-300' }}"
                                            data-role-id="{{ $role->id }}"
                                            data-module="{{ $module }}"
                                            data-all-permissions="{{ $allActionsHavePermission ? '1' : '0' }}"
                                            title="Toggle all permissions for {{ $module }}"
                                        >
                                            @if($allActionsHavePermission)
                                                <i class="fa-solid fa-check text-white text-xs"></i>
                                            @endif
                                        </button>
                                    </td>
                                @endforeach
                            </tr>
                            {{-- Action Rows (Children) --}}
                            @foreach($actions as $action)
                                @php
                                    $actionId = 'action-' . $module . '-' . $action;
                                @endphp
                                <tr 
                                    class="action-row hidden"
                                    data-module="{{ $module }}"
                                    data-action="{{ $action }}"
                                    id="row-{{ $actionId }}"
                                >
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <div class="flex items-center pl-12">
                                            <span class="text-gray-700">{{ ucfirst($action) }}</span>
                                        </div>
                                    </td>
                                    @foreach($roles as $role)
                                        @php
                                            $permissionName = $module . '.' . $action;
                                            $hasPermission = $role->permissions->contains('name', $permissionName);
                                        @endphp
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <button
                                                onclick="togglePermission({{ $role->id }}, '{{ $permissionName }}', this)"
                                                class="permission-toggle w-6 h-6 rounded border-2 transition {{ $hasPermission ? 'bg-green-500 border-green-600' : 'bg-gray-200 border-gray-300' }}"
                                                data-role-id="{{ $role->id }}"
                                                data-permission="{{ $permissionName }}"
                                                data-has-permission="{{ $hasPermission ? '1' : '0' }}"
                                                title="{{ ucfirst($action) }} permission for {{ $module }}"
                                            >
                                                @if($hasPermission)
                                                    <i class="fa-solid fa-check text-white text-xs"></i>
                                                @endif
                                            </button>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active class from all tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active', 'border-blue-500', 'text-blue-600');
            button.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Show selected tab content
        document.getElementById('content-' + tabName).classList.remove('hidden');
        
        // Add active class to selected tab
        const activeTab = document.getElementById('tab-' + tabName);
        activeTab.classList.add('active', 'border-blue-500', 'text-blue-600');
        activeTab.classList.remove('border-transparent', 'text-gray-500');
    }

    // Tree functionality
    function toggleModule(module) {
        const button = document.querySelector(`[data-module="${module}"].module-toggle`);
        if (!button) return;
        
        const isExpanded = button.getAttribute('data-expanded') === 'true';
        const icon = button.querySelector('svg');
        
        // Toggle icon rotation
        if (isExpanded) {
            button.setAttribute('data-expanded', 'false');
            icon.style.transform = 'rotate(0deg)';
            hideModuleActions(module);
        } else {
            button.setAttribute('data-expanded', 'true');
            icon.style.transform = 'rotate(90deg)';
            showModuleActions(module);
        }
    }

    function hideModuleActions(module) {
        const actionRows = document.querySelectorAll(`tr.action-row[data-module="${module}"]`);
        actionRows.forEach(row => {
            row.classList.add('hidden');
        });
    }

    function showModuleActions(module) {
        const actionRows = document.querySelectorAll(`tr.action-row[data-module="${module}"]`);
        actionRows.forEach(row => {
            row.classList.remove('hidden');
        });
    }

    function toggleModulePermission(roleId, module, button) {
        const allPermissions = button.getAttribute('data-all-permissions') === '1';
        const newState = !allPermissions;

        // Get all actions for this module
        const modules = @json($modules);
        const actions = modules[module] || [];

        // Update all action permissions for this module and role
        let updateCount = 0;
        actions.forEach((action, index) => {
            const permissionName = module + '.' + action;
            const actionButton = document.querySelector(
                `button.permission-toggle[data-role-id="${roleId}"][data-permission="${permissionName}"]`
            );
            if (actionButton) {
                // Optimistic update
                updatePermissionButton(actionButton, newState);
                // Actually update permission
                updatePermission(roleId, permissionName, newState, actionButton);
                updateCount++;
            }
        });

        // Update module button
        updatePermissionButton(button, newState);
        button.setAttribute('data-all-permissions', newState ? '1' : '0');
    }

    function updatePermissionButton(button, hasPermission) {
        button.setAttribute('data-has-permission', hasPermission ? '1' : '0');
        if (hasPermission) {
            button.classList.add('bg-green-500', 'border-green-600');
            button.classList.remove('bg-gray-200', 'border-gray-300');
            button.innerHTML = '<i class="fa-solid fa-check text-white text-xs"></i>';
        } else {
            button.classList.remove('bg-green-500', 'border-green-600');
            button.classList.add('bg-gray-200', 'border-gray-300');
            button.innerHTML = '';
        }
    }

    function togglePermission(roleId, permissionName, button) {
        const hasPermission = button.getAttribute('data-has-permission') === '1';
        const newState = !hasPermission;

        // Optimistic update
        updatePermissionButton(button, newState);

        // Update permission
        updatePermission(roleId, permissionName, newState, button);

        // Update module button if all actions are checked/unchecked
        const [module, action] = permissionName.split('.');
        updateModuleButtonState(roleId, module);
    }

    function updatePermission(roleId, permissionName, add, button) {
        // Use toggle-permission route
        fetch('{{ route("admin.permissions.roles.toggle-permission", ":id") }}'.replace(':id', roleId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                _method: 'PATCH',
                permission_name: permissionName,
                add: add
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (typeof showToast === 'function') {
                    showToast('Permission updated successfully!', 'success');
                }
            } else {
                // Revert on error
                const hasPermission = button.getAttribute('data-has-permission') === '1';
                updatePermissionButton(button, !hasPermission);
                if (typeof showToast === 'function') {
                    showToast('Error updating permission', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Revert on error
            const hasPermission = button.getAttribute('data-has-permission') === '1';
            updatePermissionButton(button, !hasPermission);
            if (typeof showToast === 'function') {
                showToast('Error updating permission', 'error');
            }
        });
    }

    function updateModuleButtonState(roleId, module) {
        const modules = @json($modules);
        const actions = modules[module] || [];
        const moduleButton = document.querySelector(
            `button.module-permission-toggle[data-role-id="${roleId}"][data-module="${module}"]`
        );
        
        if (!moduleButton) return;

        let allChecked = true;
        actions.forEach(action => {
            const permissionName = module + '.' + action;
            const actionButton = document.querySelector(
                `button.permission-toggle[data-role-id="${roleId}"][data-permission="${permissionName}"]`
            );
            if (actionButton && actionButton.getAttribute('data-has-permission') !== '1') {
                allChecked = false;
            }
        });

        updatePermissionButton(moduleButton, allChecked);
        moduleButton.setAttribute('data-all-permissions', allChecked ? '1' : '0');
    }

    function openUserPermissionsDialog(userId) {
        // Redirect to user edit page
        window.location.href = '{{ route("admin.users.form", ":id") }}?mode=edit'.replace(':id', userId);
    }
</script>

@endsection
