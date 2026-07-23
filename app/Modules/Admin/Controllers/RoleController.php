<?php

namespace App\Modules\Admin\Controllers;

use App\Core\Models\Role;
use Illuminate\Http\Request;

class RoleController
{
    public function index()
    {
        $roles = Role::all();

        return view('app', [
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name'
            ]
        ]);

        $role = Role::create([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name,' . $id
            ]
        ]);

        $role = Role::findOrFail($id);

        $role->update([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role
        ]);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully'
        ]);
    }
}
