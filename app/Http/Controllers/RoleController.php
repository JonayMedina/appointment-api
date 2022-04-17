<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json(['roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleStoreRequest $request)
    {
        $role = Role::create([
            'name' => $request->name,
            'slug' => ($request->slug) ?: substr($request->name, 0, 5),
            'active' => 1
        ]);

        return response()->json(['role' => $role], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return response()->json(['role' => $role]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(RoleStoreRequest $request, Role $role)
    {
        $role->name = $request->name;
        $role->slug = ($request->name != $role->name) ? substr($request->name, 0, 5) : $role->slug;
        $role->save();

        return response()->json(['message' => 'Role Updated', 'role' => $role]);
    }

    public function active(Role $role)
    {
        $role->active = 1;
        $role->save();
        return response()->json(['message' => 'Role Actived'], 201);
    }

    public function desactive(Role $role)
    {
        $role->active = 0;
        $role->save();
        return response()->json(['message' => 'Role Inactived'], 201);
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(['message' => 'Role Erased'], 201);
    }
}
