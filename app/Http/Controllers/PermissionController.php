<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Hiển thị màn phân quyền
     */
    public function index()
    {
        $users = User::with('roles.permissions')->latest()->paginate(10);
        $roles = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();
        
        // Danh sách các modules và actions
        $modules = [
            'users' => ['create', 'read', 'update', 'delete'],
            'categories' => ['create', 'read', 'update', 'delete'],
            'news' => ['create', 'read', 'update', 'delete'],
            'contacts' => ['create', 'read', 'update', 'delete'],
            'products' => ['create', 'read', 'update', 'delete'],
            'orders' => ['create', 'read', 'update', 'delete'],
        ];

        return view('admin.permissions.index', compact('users', 'roles', 'permissions', 'modules'));
    }

    /**
     * Quản lý Roles
     */
    public function roles()
    {
        $roles = Role::with('permissions')->orderBy('name')->paginate(10);
        $permissions = Permission::orderBy('name')->get();
        
        return view('admin.permissions.roles', compact('roles', 'permissions'));
    }

    /**
     * Show role (for AJAX)
     */
    public function showRole(Request $request, Role $role)
    {
        $role->load('permissions');
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'role' => $role,
            ]);
        }
        
        return redirect()->route('admin.permissions.roles');
    }

    /**
     * Quản lý Permissions
     */
    public function permissions()
    {
        $permissions = Permission::orderBy('name')->paginate(10);
        
        return view('admin.permissions.permissions', compact('permissions'));
    }

    /**
     * Tạo role mới
     */
    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'slug' => 'nullable|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Auto-generate slug from name if not provided
        if (empty($validated['slug']) && !empty($validated['name'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
            // Ensure uniqueness
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Role::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? true,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($validated['permissions']);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Role created successfully!',
                'role' => $role->load('permissions'),
            ]);
        }

        return redirect()->route('admin.permissions.roles')->with('success', 'Role created successfully!');
    }

    /**
     * Cập nhật role
     */
    public function updateRole(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'slug' => 'nullable|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Auto-generate slug from name if not provided
        if (empty($validated['slug']) && !empty($validated['name'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
            // Ensure uniqueness
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Role::where('slug', $validated['slug'])->where('id', '!=', $role->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $role->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? $role->slug,
            'description' => $validated['description'] ?? $role->description,
            'status' => $validated['status'] ?? $role->status,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($validated['permissions']);
        } else {
            $role->permissions()->detach();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully!',
                'role' => $role->fresh()->load('permissions'),
            ]);
        }

        return redirect()->route('admin.permissions.roles')->with('success', 'Role updated successfully!');
    }

    /**
     * Xóa role
     */
    public function destroyRole(Request $request, Role $role)
    {
        $role->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully!',
            ]);
        }

        return redirect()->route('admin.permissions.roles')->with('success', 'Role deleted successfully!');
    }

    /**
     * Toggle permission for role (quick update)
     */
    public function toggleRolePermission(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permission_name' => 'required|string',
            'add' => 'required|boolean',
        ]);

        // Find or create permission
        $permission = Permission::firstOrCreate(['name' => $validated['permission_name']]);

        if ($validated['add']) {
            $role->permissions()->syncWithoutDetaching([$permission->id]);
        } else {
            $role->permissions()->detach($permission->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully!',
            'role' => $role->fresh()->load('permissions'),
        ]);
    }

    /**
     * Tạo permission mới
     */
    public function storePermission(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        $permission = Permission::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully!',
                'permission' => $permission,
            ]);
        }

        return redirect()->route('admin.permissions.permissions')->with('success', 'Permission created successfully!');
    }

    /**
     * Cập nhật permission
     */
    public function updatePermission(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully!',
                'permission' => $permission->fresh(),
            ]);
        }

        return redirect()->route('admin.permissions.permissions')->with('success', 'Permission updated successfully!');
    }

    /**
     * Xóa permission
     */
    public function destroyPermission(Request $request, Permission $permission)
    {
        $permission->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully!',
            ]);
        }

        return redirect()->route('admin.permissions.permissions')->with('success', 'Permission deleted successfully!');
    }

    /**
     * Cập nhật permissions cho user
     */
    public function updateUserPermissions(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($request->has('roles')) {
            $user->roles()->sync($validated['roles']);
        } else {
            $user->roles()->detach();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User permissions updated successfully!',
                'user' => $user->fresh()->load('roles.permissions'),
            ]);
        }

        return redirect()->route('admin.permissions.index')->with('success', 'User permissions updated successfully!');
    }
}
