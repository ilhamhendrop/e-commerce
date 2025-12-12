<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Resources\UserDetailResource;
use App\Http\Resources\UserListResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function Register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required|unique:users',
                'name' => 'required',
                'email' => 'required|unique:users',
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(8)
                        ->mixedCase()
                        ->symbols()
                        ->numbers()
                        ->uncompromised()
                ]
            ],
            [
                'username.required' => 'Username tidak boleh kosong',
                'username.unique' => 'Username sudah digunakan',
                'name.required' => 'Nama tidak boleh kosong',
                'email.required' => 'Email tidak boleh kosong',
                'email.unique' => 'Email sudah digunakan',
                'password.required' => 'Password tidak boleh kosong',
                'password.confirmed' => 'Password tidak sama',
                'password.min' => 'Password minimal 8 karakter',
                'password.symbols' => 'Password harus mengandung simbol',
                'password.numbers' => 'Password harus mengandung angka',
                'password.mixedCase' => 'Password harus mengandung huruf besar, kecil, dan simbol',
                'password.uncompromised' => 'Password tidak boleh sudah digunakan',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => RoleEnum::USER,
        ]);

        return response()->json(['message' => 'User berhasil dibuat'], 200);
    }

    public function CreateUser(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required|unique:users',
                'name' => 'required',
                'email' => 'required|unique:users',
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(8)
                        ->mixedCase()
                        ->symbols()
                        ->numbers()
                        ->uncompromised()
                ]
            ],
            [
                'username.required' => 'Username tidak boleh kosong',
                'username.unique' => 'Username sudah digunakan',
                'name.required' => 'Nama tidak boleh kosong',
                'email.required' => 'Email tidak boleh kosong',
                'email.unique' => 'Email sudah digunakan',
                'password.required' => 'Password tidak boleh kosong',
                'password.confirmed' => 'Password tidak sama',
                'password.min' => 'Password minimal 8 karakter',
                'password.symbols' => 'Password harus mengandung simbol',
                'password.numbers' => 'Password harus mengandung angka',
                'password.mixedCase' => 'Password harus mengandung huruf besar, kecil, dan simbol',
                'password.uncompromised' => 'Password tidak boleh sudah digunakan',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => RoleEnum::ADMIN,
        ]);

        return response()->json(['message' => 'User berhasil dibuat'], 200);
    }

    public function ListUser()
    {
        $cacheKey = "user_data";
        $users = Cache::tags(['users'])->remember($cacheKey, 300, function () {
            return User::all();
        });

        return UserListResource::collection($users);
    }

    public function DetailUser($id) {
        $user = User::find($id);

        return new UserDetailResource($user);
    }

    public function UpdateUserData($id, Request $request)
    {
        $user = User::find($id);

        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required|unique:users,username,'.$user->id,
                'name' => 'required',
                'email' => 'required|unique:users,email,'.$user->id,
                'role' => 'required',
            ],
            [
                'username.required' => 'Username tidak boleh kosong',
                'username.unique' => 'Username sudah digunakan',
                'name.required' => 'Nama tidak boleh kosong',
                'email.required' => 'Email tidak boleh kosong',
                'email.unique' => 'Email sudah digunakan',
                'role.required' => 'Role tidak boleh kosong',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'name' => $request->name,
            'role' => $request->role,
        ]);

        return response()->json(['massage', 'Berhasil update data'], 200);
    }

    public function UpdateUserPassword($id, Request $request) {
        $user = User::find($id);

        $validator = Validator::make(
            $request->all(),
            [
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(8)
                        ->mixedCase()
                        ->symbols()
                        ->numbers()
                        ->uncompromised()
                ]
            ],
            [
                'password.required' => 'Password tidak boleh kosong',
                'password.confirmed' => 'Password tidak sama',
                'password.min' => 'Password minimal 8 karakter',
                'password.symbols' => 'Password harus mengandung simbol',
                'password.numbers' => 'Password harus mengandung angka',
                'password.mixedCase' => 'Password harus mengandung huruf besar, kecil, dan simbol',
                'password.uncompromised' => 'Password tidak boleh sudah digunakan',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => 'Password berhasil dirubah'], 200);
    }

    public function DeleteUser($id) {
        $user = User::find($id);
        $user->delete();

        return response()->json(['message' => 'User berhasil dihapus'], 200);
    }
}
