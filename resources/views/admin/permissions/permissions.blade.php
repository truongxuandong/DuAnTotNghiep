@extends('admin.layouts.admin')

@section('title','Permissions Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Permissions Management</h1>
        <button 
            onclick="openPermissionDialog()" 
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
        >
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add Permission
        </button>
    </div>

    <!-- Permissions Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            @php
                $columns = [
                    ['label' => '#', 'key' => '__index', 'class' => 'w-12', 'td_class' => 'font-medium'],
                    ['label' => 'Name', 'key' => 'name'],
                    ['label' => 'Created', 'key' => 'created_at'],
                ];

                $actions = [
                    [
                        'label' => 'Edit',
                        'icon' => 'fa-edit',
                        'onclick' => 'openPermissionDialog({id})',
                        'class' => 'text-blue-600 hover:text-blue-800',
                    ],
                    [
                        'label' => 'Delete',
                        'icon' => 'fa-trash',
                        'onclick' => 'openDeletePermissionDialog({id})',
                        'class' => 'text-red-600 hover:text-red-800',
                    ],
                ];
            @endphp

            <x-common-table :columns="$columns" :rows="$permissions" :showPagination="true" :actions="$actions" />
        </div>
    </div>
</div>

<!-- CREATE/EDIT PERMISSION DIALOG -->
<x-dialog 
    title="Permission" 
    subtitle=""
    size="md"
    id="permissionDialog"
>
    <x-form
        action=""
        method="POST"
        id="permissionForm"
        :fields="[
            [
                'name' => 'name',
                'label' => 'Permission Name',
                'type' => 'text',
                'placeholder' => 'e.g., users.create, news.update',
                'required' => true,
            ],
        ]"
        submitButtonText="Save Permission"
        submitButtonClass="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition"
        :showCancelButton="true"
        cancelButtonUrl="javascript:closeDialog('permissionDialog')"
    />
</x-dialog>

<!-- DELETE PERMISSION DIALOG -->
<x-dialog 
    title="Delete Permission" 
    subtitle="Are you sure you want to delete this permission?"
    size="md"
    id="deletePermissionDialog"
>
    <div class="space-y-4">
        <p class="text-gray-700">This action cannot be undone.</p>
        <form id="deletePermissionForm" action="" method="POST" class="mt-4">
            @csrf
            @method('DELETE')
            <div class="flex gap-4 justify-end">
                <button 
                    type="button"
                    onclick="closeDialog('deletePermissionDialog')"
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
    let currentPermissionId = null;

    function openPermissionDialog(permissionId = null) {
        const dialog = document.getElementById('permissionDialog');
        const dialogHeader = dialog.querySelector('.flex.items-center.justify-between');
        const dialogTitle = dialogHeader.querySelector('h2');
        let dialogSubtitle = dialogHeader.querySelector('p');
        const form = document.getElementById('permissionForm');
        const submitBtn = form.querySelector('button[type="submit"]');
        const methodInput = form.querySelector('input[name="_method"]');

        currentPermissionId = permissionId;

        if (permissionId) {
            // For edit, we need to fetch permission data
            // Since we don't have a show route, we'll get it from the table data
            const row = document.querySelector(`[data-row-id="${permissionId}"]`);
            if (row) {
                const permissionData = JSON.parse(row.getAttribute('data-row-data'));
                
                dialogTitle.textContent = 'Edit Permission';
                if (!dialogSubtitle) {
                    dialogSubtitle = document.createElement('p');
                    dialogSubtitle.className = 'text-sm text-gray-600 mt-1';
                    dialogHeader.querySelector('div').appendChild(dialogSubtitle);
                }
                dialogSubtitle.textContent = 'Update permission information';
                submitBtn.textContent = 'Update Permission';

                form.action = '{{ route("admin.permissions.permissions.update", ":id") }}'.replace(':id', permissionId);
                if (!methodInput) {
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PUT';
                    form.appendChild(methodField);
                } else {
                    methodInput.value = 'PUT';
                }

                form.querySelector('input[name="name"]').value = permissionData.name || '';
            }

            openDialog('permissionDialog');
        } else {
            dialogTitle.textContent = 'Create New Permission';
            if (!dialogSubtitle) {
                dialogSubtitle = document.createElement('p');
                dialogSubtitle.className = 'text-sm text-gray-600 mt-1';
                dialogHeader.querySelector('div').appendChild(dialogSubtitle);
            }
            dialogSubtitle.textContent = 'Add a new permission to your system';
            submitBtn.textContent = 'Create Permission';

            form.action = '{{ route("admin.permissions.permissions.store") }}';
            if (methodInput) {
                methodInput.remove();
            }

            form.reset();

            openDialog('permissionDialog');
        }
    }

    function openDeletePermissionDialog(permissionId) {
        document.getElementById('deletePermissionForm').action = '{{ route("admin.permissions.permissions.destroy", ":id") }}'.replace(':id', permissionId);
        openDialog('deletePermissionDialog');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const permissionForm = document.getElementById('permissionForm');
        const deleteForm = document.getElementById('deletePermissionForm');

        if (permissionForm) {
            permissionForm.addEventListener('submit', function(e) {
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
                        closeDialog('permissionDialog');
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Permission saved successfully!', 'success');
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
                        closeDialog('deletePermissionDialog');
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Permission deleted successfully!', 'success');
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
