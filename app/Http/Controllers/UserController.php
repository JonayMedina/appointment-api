<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function all()
    {
        $users = User::all();
        return response()->json(['users' => $users], 200);
    }

    public function byId(User $user)
    {
        return response()->json(['user' => $user]);
    }

    public function byRole($role)
    {
        $users = User::whereHas('role', function ($query) use ($role) {
            $query->where('role_id', $role);
        })->with('role')->get();
        return response()->json(['users' => $users]);
    }

    public function newUser(UserRequest $request)
    {
        $user = User::create([
            'name' => ucfirst($request->name),
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'dni' => $request->dni,
            'birthday' => $request->birthday,
            'active' => 1
        ]);

        return response()->json([
            'message' => 'User Saved',
            'user' => $user
        ], 201);
    }

    public function show(User $user)
    {
        return response()->json(['user' => $user]);
    }

    public function update(UserRequest $request, User $user)
    {
        $user->email = $request->email;
        $user->security_question = $request->security_question;
        $user->security_response = $request->security_response;
        $user->name = ucfirst($request->name);
        $user->phone = $request->phone;
        $user->dni = $request->dni;
        $user->birthday = $request->birthday;
        $user->save();

        return response()->json([
            'message' => 'User Data Updated',
            'user' => $user
        ], 201);
    }

    public function updateOwnData(UserRequest $request)
    {

        $user = User::findOrFail(Auth::user()->id);

        $user->email = $request->email;
        $user->security_question = $request->security_question;
        $user->security_response = $request->security_response;
        $user->name = ucfirst($request->name);
        $user->phone = $request->phone;
        $user->dni = $request->dni;
        $user->birthday = $request->birthday;
        $user->save();


        return response()->json([
            'message' => 'Own Data Updated',
            'user' => $user
        ], 201);
    }

    public function checkAuth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'incorrecto', 'message' => $validator->getMessageBag()], 400);
        }

        $auth = auth('api')->user();
        if (!Hash::check($request->password, $auth->password)) {
            return response()->json(['error' => 'Password not Match!!'], 401);
        }

        return response()->json(['message' => true], 200);
    }

    public function updateSecurityQuestions(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'security_question' => 'required|string|between:2,100',
            'security_response' => 'required|string|between:2,100'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 400]);
        }

        $auth = auth('api')->user();
        $user = User::find($auth->id);
        $user->update([
            'security_question' => $request->security_question,
            'security_response' => $request->security_response,
        ]);

        return response()->json([
            'message' => 'Pregunta y respuesta de Seguridad Actualizada', 'user' => $user
        ], 201);
    }

    public function profileUpdatePassword(ChangePasswordRequest $request)
    {
        $auth = Auth::user();
        $user = User::find($auth->id);
        $user->update([
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'message' => 'Access Updated'
        ], 201);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $checkToken = $this->findToken($request->email, $request->token);
        if (!$checkToken) {
            return response()->json(['error' => 'No encontramos este Token o ha Expirado!!'], 404);
        }

        User::where('email', $request->email)->update([
            'password' => bcrypt($request->password),
        ]);
        $this->deleteToken($request->token);

        return response()->json([
            'message' => ' Access'
        ], 201);
    }

    public function getAvatar(User $user)
    {
        $avatar = ($user->avatar) ? Storage::url($user->avatar) : '';
        if ($avatar) {
            $avatar = url('/') . $avatar;
        }

        return response()->json(['avatar' => $avatar], 200);
    }

    public function updateAvatar(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 400]);
        }

        $image = $request->file('avatar');
        if ($user->avatar) {
            $delete[] = Storage::disk('public')->delete($user->avatar);
        }
        $user->avatar = $this->saveAvatar($image, 'users/', $user->id);

        $user->save();
        $avatar = Storage::url($user->avatar);
        return response()->json(
            [
                'message' => 'Image Saved',
                'avatar' => url('/') . $avatar
            ],
            201
        );
    }
    public function deleteUser(User $user)
    {

        $user->delete();

        return response()->json(['message' => 'User erased']);
    }
}
