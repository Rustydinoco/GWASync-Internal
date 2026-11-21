<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index()
    {
        return Inertia::render('Access/Roles/Index', [
            'roles' => Role::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:roles,code',
            'description' => 'nullable|string',
        ]);
        Role::create($validated);
        return redirect()->back()->with('success', 'Role berhasil ditambahkan.');
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:roles,code,' . $role->id,
            'description' => 'nullable|string',
        ]);
        $role->update($validated);
        return redirect()->back()->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->back()->with('success', 'Role berhasil dihapus.');
    }
}
