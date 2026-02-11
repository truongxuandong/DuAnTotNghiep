@extends('admin.layouts.admin')

@section('title','Users Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Users Management</h1>
        <button 
            onclick="openUserDialog()" 
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
        >
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add User
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            @php
                $columns = [
                    ['label' => '#', 'key' => '__index', 'class' => 'w-12', 'td_class' => 'font-medium'],
                    ['label' => 'Name', 'key' => 'name'],
                    ['label' => 'Email', 'key' => 'email'],
                    ['label' => 'Phone', 'key' => 'phone'],
                    ['label' => 'Roles', 'key' => 'roles', 'type' => 'roles-list'],
                    ['label' => 'Status', 'key' => 'status', 'type' => 'status-boolean'],
                    ['label' => 'Created', 'key' => 'created_at'],
                ];

                $actions = [
                    [
                        'label' => 'View',
                        'icon' => 'fa-eye',
                        'onclick' => 'openUserViewDialog({id})',
                        'class' => 'text-green-600 hover:text-green-800',
                    ],
                    [
                        'label' => 'Edit',
                        'icon' => 'fa-edit',
                        'onclick' => 'openUserDialog({id})',
                        'class' => 'text-blue-600 hover:text-blue-800',
                    ],
                    [
                        'label' => 'Delete',
                        'icon' => 'fa-trash',
                        'onclick' => 'openDeleteUserDialog({id})',
                        'class' => 'text-red-600 hover:text-red-800',
                    ],
                ];
            @endphp

            <x-common-table :columns="$columns" :rows="$rows" :showPagination="true" :actions="$actions" />
        </div>
    </div>
</div>

<!-- ================================================
     CREATE/EDIT USER DIALOG WITH FORM
     ================================================ -->
<x-dialog 
    title="User" 
    subtitle=""
    size="md"
    id="userDialog"
>
    <x-form
        action=""
        method="POST"
        id="userForm"
        :fields="[
            [
                'name' => 'name',
                'label' => 'Full Name',
                'type' => 'text',
                'placeholder' => 'Enter user name',
                'required' => true,
            ],
            [
                'name' => 'email',
                'label' => 'Email Address',
                'type' => 'email',
                'placeholder' => 'user@example.com',
                'required' => true,
            ],
            [
                'name' => 'phone',
                'label' => 'Phone Number',
                'type' => 'tel',
                'placeholder' => '+1 (555) 123-4567',
            ],
            [
                'name' => 'password',
                'label' => 'Password',
                'type' => 'password',
                'placeholder' => 'Enter password (min 8 characters)',
                'required' => true,
            ],
            [
                'name' => 'password_confirmation',
                'label' => 'Confirm Password',
                'type' => 'password',
                'placeholder' => 'Confirm password',
                'required' => true,
            ],
            [
                'name' => 'role',
                'label' => 'Role',
                'type' => 'select',
                'options' => [
                    'Customer' => 'customer',
                    'Admin' => 'admin',
                ],
                'value' => 'customer',
            ],
            [
                'name' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    'Active' => 1,
                    'Inactive' => 0,
                ],
                'value' => 1,
                'required' => true,
            ],
            [
                'name' => 'roles',
                'label' => 'Assign Roles',
                'type' => 'checkbox-group',
                'options' => $roles->pluck('name', 'id')->toArray(),
            ],
        ]"
        submitButtonText="Save User"
        submitButtonClass="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition"
        :showCancelButton="true"
        cancelButtonUrl="javascript:closeDialog('userDialog')"
    />
</x-dialog>

<!-- ================================================
     VIEW USER DIALOG (READ-ONLY)
     ================================================ -->
<x-dialog 
    title="View User" 
    subtitle=""
    size="md"
    id="userViewDialog"
>
    <div id="userViewContent" class="space-y-6">
        <!-- Content will be loaded via JavaScript -->
    </div>
    <div class="mt-6 flex justify-end">
        <button 
            onclick="closeDialog('userViewDialog')"
            class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-medium transition"
        >
            Close
        </button>
    </div>
</x-dialog>

<!-- DELETE USER CONFIRM DIALOG -->
<x-dialog 
    title="Delete User" 
    subtitle="Are you sure you want to delete this user?"
    size="md"
    id="deleteUserDialog"
>
    <div class="space-y-4">
        <p class="text-gray-700">This action cannot be undone.</p>
        <form id="deleteUserForm" action="" method="POST" class="mt-4">
            @csrf
            @method('DELETE')
            <div class="flex gap-4 justify-end">
                <button 
                    type="button"
                    onclick="closeDialog('deleteUserDialog')"
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
    let currentUserId = null;
    const allRoles = @json($roles->pluck('name', 'id')->toArray());

    function openUserViewDialog(userId) {
        const dialog = document.getElementById('userViewDialog');
        const dialogHeader = dialog.querySelector('.flex.items-center.justify-between');
        const dialogTitle = dialogHeader.querySelector('h2');
        const contentDiv = document.getElementById('userViewContent');

        // Show loading
        contentDiv.innerHTML = '<div class="text-center py-4"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>';

        // Fetch user data
        fetch('{{ route("admin.users.show", ":id") }}'.replace(':id', userId), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success || !data.user) {
                if (typeof showToast === 'function') {
                    showToast('User not found', 'error');
                }
                return;
            }

            const user = data.user;
            dialogTitle.textContent = 'View User: ' + user.name;

            const statusText = user.status ? 'Active' : 'Inactive';
            const roleText = user.role === 'admin' ? 'Admin' : 'Customer';
            const rolesList = user.roles && user.roles.length > 0 
                ? user.roles.map(r => r.name).join(', ') 
                : 'No roles assigned';
            const createdDate = user.created_at ? new Date(user.created_at).toLocaleString('vi-VN') : 'N/A';
            const updatedDate = user.updated_at ? new Date(user.updated_at).toLocaleString('vi-VN') : 'N/A';

            contentDiv.innerHTML = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input 
                            type="text" 
                            value="${(user.name || '').replace(/"/g, '&quot;')}" 
                            disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input 
                            type="email" 
                            value="${(user.email || '').replace(/"/g, '&quot;')}" 
                            disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input 
                            type="text" 
                            value="${(user.phone || 'N/A').replace(/"/g, '&quot;')}" 
                            disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <input 
                            type="text" 
                            value="${roleText}" 
                            disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assigned Roles</label>
                        <input 
                            type="text" 
                            value="${rolesList.replace(/"/g, '&quot;')}" 
                            disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <input 
                            type="text" 
                            value="${statusText}" 
                            disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed"
                        />
                    </div>
                    <div class="pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Created:</span>
                                <span class="ml-2 text-gray-900">${createdDate}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Updated:</span>
                                <span class="ml-2 text-gray-900">${updatedDate}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            openDialog('userViewDialog');
        })
        .catch(error => {
            console.error('Error:', error);
            contentDiv.innerHTML = '<div class="text-red-600 py-4">Error loading user data</div>';
            if (typeof showToast === 'function') {
                showToast('Error loading user', 'error');
            }
        });
    }

    function openUserDialog(userId = null) {
        const dialog = document.getElementById('userDialog');
        const dialogHeader = dialog.querySelector('.flex.items-center.justify-between');
        const dialogTitle = dialogHeader.querySelector('h2');
        let dialogSubtitle = dialogHeader.querySelector('p');
        const form = document.getElementById('userForm');
        const submitBtn = form.querySelector('button[type="submit"]');
        const methodInput = form.querySelector('input[name="_method"]');

        currentUserId = userId;

        if (userId) {
            // Edit mode - Fetch user data via AJAX
            fetch('{{ route("admin.users.show", ":id") }}'.replace(':id', userId), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.user) {
                    if (typeof showToast === 'function') {
                        showToast('User not found', 'error');
                    }
                    return;
                }

                const user = data.user;

                // Update dialog title
                dialogTitle.textContent = 'Edit User';
                if (!dialogSubtitle) {
                    dialogSubtitle = document.createElement('p');
                    dialogSubtitle.className = 'text-sm text-gray-600 mt-1';
                    dialogHeader.querySelector('div').appendChild(dialogSubtitle);
                }
                dialogSubtitle.textContent = 'Update user information';
                submitBtn.textContent = 'Update User';

                // Set form action và method
                form.action = '{{ route("admin.users.update", ":id") }}'.replace(':id', userId);
                if (!methodInput) {
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PUT';
                    form.appendChild(methodField);
                } else {
                    methodInput.value = 'PUT';
                }

                // Populate form fields
                form.querySelector('input[name="name"]').value = user.name || '';
                form.querySelector('input[name="email"]').value = user.email || '';
                form.querySelector('input[name="phone"]').value = user.phone || '';
                form.querySelector('select[name="role"]').value = user.role || 'customer';
                form.querySelector('select[name="status"]').value = user.status ? 1 : 0;

                // Handle password fields - make optional for edit
                const passwordField = form.querySelector('input[name="password"]');
                const passwordConfField = form.querySelector('input[name="password_confirmation"]');
                if (passwordField) {
                    passwordField.required = false;
                    passwordField.placeholder = 'Leave blank to keep current password';
                }
                if (passwordConfField) {
                    passwordConfField.required = false;
                    passwordConfField.placeholder = 'Leave blank to keep current password';
                }

                // Handle roles checkboxes
                const roleCheckboxes = form.querySelectorAll('input[type="checkbox"][name="roles[]"]');
                const userRoleIds = user.roles ? user.roles.map(r => r.id) : [];
                roleCheckboxes.forEach(checkbox => {
                    checkbox.checked = userRoleIds.includes(parseInt(checkbox.value));
                });

                openDialog('userDialog');
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof showToast === 'function') {
                    showToast('Error loading user', 'error');
                }
            });
        } else {
            // Create mode
            dialogTitle.textContent = 'Create New User';
            if (!dialogSubtitle) {
                dialogSubtitle = document.createElement('p');
                dialogSubtitle.className = 'text-sm text-gray-600 mt-1';
                dialogHeader.querySelector('div').appendChild(dialogSubtitle);
            }
            dialogSubtitle.textContent = 'Add a new user to your system';
            submitBtn.textContent = 'Create User';

            // Set form action và method
            form.action = '{{ route("admin.users.store") }}';
            if (methodInput) {
                methodInput.remove();
            }

            // Clear form fields
            form.reset();
            form.querySelector('select[name="status"]').value = 1;
            form.querySelector('select[name="role"]').value = 'customer';

            // Reset password fields
            const passwordField = form.querySelector('input[name="password"]');
            const passwordConfField = form.querySelector('input[name="password_confirmation"]');
            if (passwordField) {
                passwordField.required = true;
                passwordField.placeholder = 'Enter password (min 8 characters)';
            }
            if (passwordConfField) {
                passwordConfField.required = true;
                passwordConfField.placeholder = 'Confirm password';
            }

            // Uncheck all role checkboxes
            const roleCheckboxes = form.querySelectorAll('input[type="checkbox"][name="roles[]"]');
            roleCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            openDialog('userDialog');
        }
    }

    function openDeleteUserDialog(userId) {
        document.getElementById('deleteUserForm').action = '{{ route("admin.users.destroy", ":id") }}'.replace(':id', userId);
        openDialog('deleteUserDialog');
    }

    // Handle form submission với AJAX
    document.addEventListener('DOMContentLoaded', function() {
        const userForm = document.getElementById('userForm');
        const deleteForm = document.getElementById('deleteUserForm');

        if (userForm) {
            userForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                
                // Disable button và show loading
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
                        closeDialog('userDialog');
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'User saved successfully!', 'success');
                        }
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Something went wrong', 'error');
                        } else {
                            alert('Error: ' + (data.message || 'Something went wrong'));
                        }
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof showToast === 'function') {
                        showToast('Error: Something went wrong', 'error');
                    } else {
                        alert('Error: Something went wrong');
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
                        closeDialog('deleteUserDialog');
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'User deleted successfully!', 'success');
                        }
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Something went wrong', 'error');
                        } else {
                            alert('Error: ' + (data.message || 'Something went wrong'));
                        }
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof showToast === 'function') {
                        showToast('Error: Something went wrong', 'error');
                    } else {
                        alert('Error: Something went wrong');
                    }
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                });
            });
        }
    });
</script>

@endsection
