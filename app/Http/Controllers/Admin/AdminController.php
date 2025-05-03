<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.manage_users', compact('users'));
    }

    public function createUser()
    {
        return view('admin.create_user');
    }

    public function storeUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5',
            'user_type' => 'required|in:aspirant,employer,admin'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->user_type = $request->user_type;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'User created successfully.',
                'redirect' => route('admin.manage_users')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'errors' => ['general' => ['An error occurred while creating the user: ' . $e->getMessage()]]
            ]);
        }
    }

    public function destroyUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.manage_users')->with('success', 'User deleted successfully.');
    }
}
