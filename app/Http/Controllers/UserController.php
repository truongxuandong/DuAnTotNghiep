<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Danh sách users (phân trang + tìm kiếm)
     */
    public function index(Request $request)
    {
        $query = User::with('roles')->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $rows = $query->paginate(10)->withQueryString();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('rows', 'search', 'roles'));
    }

    /**
     * Hiển thị form tạo user mới hoặc edit/view
     */
    public function form(Request $request, User $user = null)
    {
        // Get mode from query parameter or default to 'view'
        $mode = $request->get('mode', 'view');
        
        // Validate mode
        if (!in_array($mode, ['create', 'edit', 'view'])) {
            $mode = 'view';
        }

        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        if ($mode === 'create') {
            $user = null;
            return view('admin.users.form', compact('roles', 'permissions', 'mode', 'user'));
        }

        if (!$user) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }
            return redirect()->route('admin.users.index')->with('error', 'User not found');
        }

        // Load relationships
        $user->load('roles.permissions');

        if ($request->ajax() && $mode === 'view') {
            return response()->json([
                'success' => true,
                'user' => $user,
            ]);
        }

        return view('admin.users.form', compact('user', 'roles', 'permissions', 'mode'));
    }

    /**
     * Show user (backward compatibility)
     */
    public function show(Request $request, User $user)
    {
        $user->load('roles');
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'user' => $user,
            ]);
        }
        
        $request->merge(['mode' => 'view']);
        return $this->form($request, $user);
    }

    /**
     * Tạo user mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string|in:customer,admin',
            'status' => 'required|boolean',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Assign roles
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User created successfully!',
                'user' => $user->load('roles'),
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    /**
     * Cập nhật user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'nullable|string|in:customer,admin',
            'status' => 'required|boolean',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        // Sync roles
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        } else {
            $user->roles()->detach();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully!',
                'user' => $user->fresh()->load('roles'),
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Xoá user
     */
    public function destroy(Request $request, User $user)
    {
        $user->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully!',
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }
}
