@extends('admin.layouts.admin')

@section('title','Roles Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Roles Management</h1>
        <button 
            onclick="openRoleDialog()" 
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
        >
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add Role
        </button>
    </div>

    <!-- Roles Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            @php
                $columns = [
                    ['label' => '#', 'key' => '__index', 'class' => 'w-12', 'td_class' => 'font-medium'],
                    ['label' => 'Name', 'key' => 'name'],
                    ['label' => 'Slug', 'key' => 'slug'],
                    ['label' => 'Description', 'key' => 'description'],
                    ['label' => 'Status', 'key' => 'status', 'type' => 'status-boolean'],
                    ['label' => 'Permissions', 'key' => 'permissions', 'type' => 'roles-list'],
                    ['label' => 'Created', 'key' => 'created_at'],
                ];

                $actions = [
                    [
                        'label' => 'Edit',
                        'icon' => 'fa-edit',
                        'onclick' => 'openRoleDialog({id})',
                        'class' => 'text-blue-600 hover:text-blue-800',
                    ],
                    [
                        'label' => 'Delete',
                        'icon' => 'fa-trash',
                        'onclick' => 'openDeleteRoleDialog({id})',
                        'class' => 'text-red-600 hover:text-red-800',
                    ],
                ];
            @endphp

            <x-common-table :columns="$columns" :rows="$roles" :showPagination="true" :actions="$actions" />
        </div>
    </div>
</div>

<!-- CREATE/EDIT ROLE DIALOG -->
<x-dialog 
    title="Role" 
    subtitle=""
    size="md"
    id="roleDialog"
>
    <x-form
        action=""
        method="POST"
        id="roleForm"
        :fields="[
            [
                'name' => 'name',
                'label' => 'Role Name',
                'type' => 'text',
                'placeholder' => 'Enter role name',
                'required' => true,
            ],
            [
                'name' => 'slug',
                'label' => 'Slug',
                'type' => 'text',
                'placeholder' => 'Auto-generated from name if empty',
                'required' => false,
            ],
            [
                'name' => 'description',
                'label' => 'Description',
                'type' => 'textarea',
                'placeholder' => 'Enter role description',
                'required' => false,
            ],
            [
                'name' => 'status',
                'label' => 'Status',
                'type' => 'checkbox',
                'value' => '1',
                'checked' => true,
            ],
            [
                'name' => 'permissions',
                'label' => 'Assign Permissions',
                'type' => 'checkbox-group',
                'options' => $permissions->pluck('name', 'id')->toArray(),
            ],
        ]"
        submitButtonText="Save Role"
        submitButtonClass="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition"
        :showCancelButton="true"
        cancelButtonUrl="javascript:closeDialog('roleDialog')"
    />
</x-dialog>

<!-- DELETE ROLE DIALOG -->
<x-dialog 
    title="Delete Role" 
    subtitle="Are you sure you want to delete this role?"
    size="md"
    id="deleteRoleDialog"
>
    <div class="space-y-4">
        <p class="text-gray-700">This action cannot be undone.</p>
        <form id="deleteRoleForm" action="" method="POST" class="mt-4">
            @csrf
            @method('DELETE')
            <div class="flex gap-4 justify-end">
                <button 
                    type="button"
                    onclick="closeDialog('deleteRoleDialog')"
                    class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-medium transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                >
                    Delete
                </button>
            </div>
        </form>
    </div>
</x-dialog>

<script>
    let currentRoleId = null;
    const allPermissions = @json($permissions->pluck('name', 'id')->toArray());

    function openRoleDialog(roleId = null) {
        const dialog = document.getElementById('roleDialog');
        const dialogHeader = dialog.querySelector('.flex.items-center.justify-between');
        const dialogTitle = dialogHeader.querySelector('h2');
        let dialogSubtitle = dialogHeader.querySelector('p');
        const form = document.getElementById('roleForm');
        const submitBtn = form.querySelector('button[type="submit"]');
        const methodInput = form.querySelector('input[name="_method"]');

        currentRoleId = roleId;

        if (roleId) {
            fetch('{{ route("admin.permissions.roles.show", ":id") }}'.replace(':id', roleId), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.role) {
                    if (typeof showToast === 'function') {
                        showToast('Role not found', 'error');
                    }
                    return;
                }

                const role = data.role;

                dialogTitle.textContent = 'Edit Role';
                if (!dialogSubtitle) {
                    dialogSubtitle = document.createElement('p');
                    dialogSubtitle.className = 'text-sm text-gray-600 mt-1';
                    dialogHeader.querySelector('div').appendChild(dialogSubtitle);
                }
                dialogSubtitle.textContent = 'Update role information';
                submitBtn.textContent = 'Update Role';

                form.action = '{{ route("admin.permissions.roles.update", ":id") }}'.replace(':id', roleId);
                if (!methodInput) {
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PUT';
                    form.appendChild(methodField);
                } else {
                    methodInput.value = 'PUT';
                }

                form.querySelector('input[name="name"]').value = role.name || '';
                
                const slugInput = form.querySelector('input[name="slug"]');
                if (slugInput) {
                    slugInput.value = role.slug || '';
                }
                
                const descriptionTextarea = form.querySelector('textarea[name="description"]');
                if (descriptionTextarea) {
                    descriptionTextarea.value = role.description || '';
                }
                
                const statusCheckbox = form.querySelector('input[name="status"]');
                if (statusCheckbox) {
                    statusCheckbox.checked = role.status !== undefined ? role.status : true;
                }

                const permissionCheckboxes = form.querySelectorAll('input[type="checkbox"][name="permissions[]"]');
                const rolePermissionIds = role.permissions ? role.permissions.map(p => p.id) : [];
                permissionCheckboxes.forEach(checkbox => {
                    checkbox.checked = rolePermissionIds.includes(parseInt(checkbox.value));
                });

                openDialog('roleDialog');
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof showToast === 'function') {
                    showToast('Error loading role', 'error');
                }
            });
        } else {
            dialogTitle.textContent = 'Create New Role';
            if (!dialogSubtitle) {
                dialogSubtitle = document.createElement('p');
                dialogSubtitle.className = 'text-sm text-gray-600 mt-1';
                dialogHeader.querySelector('div').appendChild(dialogSubtitle);
            }
            dialogSubtitle.textContent = 'Add a new role to your system';
            submitBtn.textContent = 'Create Role';

            form.action = '{{ route("admin.permissions.roles.store") }}';
            if (methodInput) {
                methodInput.remove();
            }

            form.reset();

            // Reset status checkbox to checked by default
            const statusCheckbox = form.querySelector('input[name="status"]');
            if (statusCheckbox) {
                statusCheckbox.checked = true;
            }

            const permissionCheckboxes = form.querySelectorAll('input[type="checkbox"][name="permissions[]"]');
            permissionCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            openDialog('roleDialog');
        }
    }

    function openDeleteRoleDialog(roleId) {
        document.getElementById('deleteRoleForm').action = '{{ route("admin.permissions.roles.destroy", ":id") }}'.replace(':id', roleId);
        openDialog('deleteRoleDialog');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const roleForm = document.getElementById('roleForm');
        const deleteForm = document.getElementById('deleteRoleForm');

        if (roleForm) {
            roleForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                
                submitBtn.disabled = true;
                submitBtn.textContent = 'Saving...';

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeDialog('roleDialog');
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Role saved successfully!', 'success');
                        }
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Something went wrong', 'error');
                        }
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof showToast === 'function') {
                        showToast('Error: Something went wrong', 'error');
                    }
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                });
            });
        }

        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;

                submitBtn.disabled = true;
                submitBtn.textContent = 'Deleting...';

                fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeDialog('deleteRoleDialog');
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Role deleted successfully!', 'success');
                        }
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Something went wrong', 'error');
                        }
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof showToast === 'function') {
                        showToast('Error: Something went wrong', 'error');
                    }
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                });
            });
        }
    });
</script>

@endsection
