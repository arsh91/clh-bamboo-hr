<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Get Users Without Super Admin
        $users = User::whereHas('role', function($q) {
            $q->where('name', '!=', 'SUPER_ADMIN');
        })->get();

        return view('users.index',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddUser $request)
    {
         //Request Data validation
         $validatedData = $request->validated();

         //Create User
         $user = User::create([
             'first_name' => $validatedData['first_name'],
             'last_name' => $validatedData['last_name'],
             'email' => $validatedData['email'],
             'phone' => $validatedData['phone'],
             'role_id' => 2,
             'password' =>  Hash::make($validatedData['password']),
             'status' => 'active',
             'created_at' => now(),
             'updated_at' => now(),
         ]);
         if($user){
            return response()->json(['success' => true, 'message' => 'User created successfully']);
         }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true, 'message' => 'User Deleted successfully']);
    }
}
