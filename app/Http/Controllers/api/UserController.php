<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use JWTAuth;

class UserController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */

    public function updateUser(Request $request) 
    {   
        $id = JWTAuth::user()->id;

        $payload = [
            'name' => $request->name,
            'user_name' => $request->user_name,
            'password'  => bcrypt($request->password),
        ];

        $user = User::where('id', $id)->update($payload);  

        if ($user) {
            return response()->json(['message' => 'Successfully updated your account']);
        } else {
            return response()->json(['message' => 'Encountered an error'], Response::HTTP_BAD_REQUEST);
        }
    }
}