<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Movie;
use App\Models\Booking;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|manager|staff']);
    }

    public function dashboard()
    {
        if (!auth()->user()->hasRole(['admin', 'manager', 'staff'])) {
            abort(403, 'Unauthorized action.');
        }

        $stats = [
            'total_users' => User::count(),
            'total_movies' => Movie::count(),
            'total_bookings' => Booking::count(),
            'recent_bookings' => Booking::with(['user', 'showtime.movie'])
                ->latest()
                ->take(5)
                ->get()
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function users()
    {
        if (!auth()->user()->hasRole(['admin', 'manager'])) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::with('roles')->paginate(10);
        return response()->json($users);
    }

    public function userDetails(User $user)
    {
        $user->load(['roles', 'bookings' => function($query) {
            $query->with(['showtime.movie', 'seats']);
        }]);
        return response()->json($user);
    }

    public function suspendUser(User $user)
    {
        $user->update(['is_active' => false]);
        return response()->json(['message' => 'User suspended successfully']);
    }

    public function activateUser(User $user)
    {
        $user->update(['is_active' => true]);
        return response()->json(['message' => 'User activated successfully']);
    }

    public function deleteUser(User $user)
    {
        DB::transaction(function() use ($user) {
            $user->bookings()->delete();
            $user->delete();
        });
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function createUser()
    {
        if (!auth()->user()->hasRole(['admin', 'manager'])) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::all();
         if (!auth()->user()->hasRole('admin')) {
             $roles = $roles->where('name', '!=', 'admin');
        }

        return view('admin.users.create', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        if (!auth()->user()->hasRole(['admin', 'manager'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'credit' => 'nullable|numeric|min:0'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'credit' => $validated['credit'] ?? 0.00,
        ]);

        if (!auth()->user()->hasRole('admin')) {
            $validated['roles'] = array_diff($validated['roles'], [Role::where('name', 'admin')->first()->id]);
        }

        $user->syncRoles($validated['roles']);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully');
    }

    public function editUser(User $user)
    {
        if (!auth()->user()->hasRole(['admin', 'manager'])) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::all();
         if (!auth()->user()->hasRole('admin')) {
             $roles = $roles->where('name', '!=', 'admin');
         }

         if (!auth()->user()->hasRole('admin') && $user->hasRole('admin')) {
             abort(403, 'Unauthorized action.');
         }

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        if (!auth()->user()->hasRole(['admin', 'manager'])) {
            abort(403, 'Unauthorized action.');
        }

        if (!auth()->user()->hasRole('admin') && $user->hasRole('admin')) {
             abort(403, 'Unauthorized action.');
         }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'credit' => 'required|numeric|min:0'
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'credit' => $validated['credit']
        ]);

        if ($validated['password']) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        if (!auth()->user()->hasRole('admin')) {
             $validated['roles'] = array_diff($validated['roles'], [Role::where('name', 'admin')->first()->id]);
             if ($user->hasRole('admin')) {
                 $validated['roles'][] = Role::where('name', 'admin')->first()->id;
                 $validated['roles'] = array_unique($validated['roles']);
             }
        }

        $user->syncRoles($validated['roles']);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroyUser(User $user)
    {
        if (!auth()->user()->hasRole(['admin', 'manager'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Cannot delete your own account']);
        }

         if (!auth()->user()->hasRole('admin') && $user->hasRole('admin')) {
             abort(403, 'Unauthorized action.');
         }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }

    public function roles()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::with('permissions')->get();
        return response()->json($roles);
    }

    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'required|array'
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role->load('permissions')
        ], 201);
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'required|array'
        ]);

        $user->syncRoles($request->roles);
        return response()->json(['message' => 'Roles assigned successfully']);
    }

    public function editRole(Role $role)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

         if (!auth()->user()->hasRole('admin') && in_array($role->name, ['admin', 'manager', 'staff', 'customer', 'guest'])) {
             abort(403, 'Unauthorized action.');
         }

        $permissions = Permission::all();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function updateRole(Request $request, Role $role)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

         if (!auth()->user()->hasRole('admin') && in_array($role->name, ['admin', 'manager', 'staff', 'customer', 'guest'])) {
             abort(403, 'Unauthorized action.');
         }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'required|array'
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions']);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully');
    }

    public function destroyRole(Role $role)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

         if (!auth()->user()->hasRole('admin') && in_array($role->name, ['admin', 'manager', 'staff', 'customer', 'guest'])) {
             abort(403, 'Unauthorized action.');
         }

        if ($role->users()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete role with assigned users']);
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully');
    }
} 