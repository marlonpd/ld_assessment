<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ConfirmPinRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\InvitedEmail;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */

    /**
     * Return the user's access token.
     */
    public function authenticate(LoginRequest $request)
    {
        $input = $request->only('email', 'password');
        $jwt_token = null;

        if (!$jwt_token = JWTAuth::attempt($input)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::where('email', $request->email)->first();

        return response()->json([
            'success' => true,
            'user'  => $user,
            'token' => $jwt_token,
        ]);
    }

    public function logout() 
    {
        JWTAuth::logout();
        
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function register(RegisterRequest $request) 
    {
        $userInvited = InvitedEmail::where('code', $request->code )->first();

        if (!$userInvited) {
            return response()->json(['message' => 'Invalid code.'], Response::HTTP_BAD_REQUEST);
        }

        $registeredUser = User::where('email', $userInvited->email)->first();

        if ($registeredUser) {
            return response()->json([
                'message' => 'Email already registered.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $generatedPin = mt_rand(100000, 999999);

        $payload = [
            'name' => 'name',
            'user_name' => $request->user_name,
            'email'     => $userInvited->email,
            'pin'       => $generatedPin,
            'password'  => bcrypt($request->password),
            'user_role' => config('config.roles.user'), 
            'registered_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $user = User::create($payload);

        $toName = $userInvited->email;
        $toEmail = $userInvited->email;
        $fromEmail = config('config.fromEmail');
        $data = array(
            'pin' => $generatedPin,
            'email' => $toEmail,
        );

        \Mail::send('mails.user_pin', $data, function($message) use ($toName, $toEmail, $fromEmail) {
            $message->to($toEmail, $toName)
            ->subject('User Registration PIN');
            $message->from($fromEmail , 'User Registration PIN');
        });

        if ($user) {
            return response()->json(['message' => 'To confirm, a 6 digit pin has been sent to your email.']);
        } else {
            return response()->json(['message' => 'Encountered an error'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function confirmPin(ConfirmPinRequest $request) 
    {
        $registeredUser = User::where('email', $request->email)->where('pin', $request->pin )->first();

        if (!$registeredUser) {
            return response()->json(['message' => 'Invalid pin or email.'], Response::HTTP_BAD_REQUEST);
        }

        $payload = [
            'is_verified' => true,
        ];

        $user = User::where('id', $registeredUser->id)->update($payload);  


        return response()->json(['message' => 'Successfully registered.']);
    }

     /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

}