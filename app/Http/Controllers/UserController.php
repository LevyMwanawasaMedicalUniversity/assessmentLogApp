<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use Spatie\Permission\Models\Role;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rules\Password;
use OwenIt\Auditing\Models\Audit;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('name')) {
            $users = User::where('email', 'like', "%{$request->name}%")
                ->latest()
                ->paginate(15);
        } else {
            $users = User::latest()
                ->paginate(15);
        }
        return view('admin.users.index', compact('users'));
    }

    public function resetUserPassword($userId)
    {
        $user = User::find($userId);
        
        $user->update([                    
            'password' => Hash::make('12345678')
        ]);
            
        return redirect()->back()->with('success', 'Password reset successfully.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::latest()->get();
        return view('admin.users.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(User $user)
    // {
    //     $user->create(array_merge($user->validated(), [
    //         'password' => 'test' 
    //     ]));

    //     return redirect()->route('users.index')
    //         ->withSuccess(__('User created successfully.'));
    // }

    public function store(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users',
        
        // Add more validation rules as per your requirements
    ]);

    $user = User::create(array_merge($validatedData, [
        'password' => Hash::make('Welcome@123')
    ]));

    $user->syncRoles($request->get('role'));

    return redirect()->route('users.index')
        ->withSuccess(__('User created successfully.'));
}

    /**
     * Display the specified resource.
     */
    public function show(User $user) 
    {
        return view('admin.users.show', [
            'user' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user) 
    {
        return view('admin.users.edit', [
            'user' => $user,
            'userRole' => $user->roles->pluck('name')->toArray(),
            'roles' => Role::latest()->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(User $user, UpdateUserRequest $request) 
    // {
    //     $user->update($request->validated());

    //     $user->syncRoles($request->get('role'));

    //     return redirect()->route('users.index')
    //         ->withSuccess(__('User updated successfully.'));
    // }

    public function update(Request $request,$userId)
    {
        // Define your custom validation rules for the email and name fields
        $request->validate([
            'name' => ['required', 'min:3'],

            'email' => [
                'required',
                'email',
                Rule::unique((new User)->getTable())->ignore($userId),
            ],
        ]);       

        $user = User::find($userId);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->save();
        
        $user->syncRoles($request->get('role'));

        return redirect()->back()->with('success', 'Updated Succesfully.');
    }

    public function resetPassword(Request $request, $userId) 
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'password.required' => 'The new password field is required.',
            'password.confirmed' => 'The new password confirmation does not match.',
            // Add any other custom error messages for the 'password' field if needed.
        ]);

        $user = User::find($userId);
        $user->password = Hash::make($request->input('password'));
        $user->save();      

        return redirect()->route('users.index')
            ->withSuccess(__('User password updated successfully.'));    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user) 
    {
        $user->delete();

        return redirect()->route('users.index')
            ->withSuccess(__('User deleted successfully.'));
    }
}
