<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SendInviteRequest;
use App\Models\User;
use App\Models\InvitedEmail;
use Symfony\Component\HttpFoundation\Response;
use App\Event\UserInvited;

class InviteUserController extends Controller
{
     /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    
    public function sendInvite(SendInviteRequest $request)
    {
        $registeredUser = User::where('email', $request->email)->first();

        if ($registeredUser) {
            return response()->json([
                'message' => 'Email already registered.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $invitedUser = InvitedEmail::firstOrNew(array('email' => $request->email));
        $invitedUser->code =$this->generateRandomString();
        $invitedUser->save();

        $data = [
            'toName' => $request->email,
            'toEmail' => $request->email, 
            'code'  => $invitedUser->code
        ];

        event(new UserInvited($data));
        
        return response()->json(['message' => 'Successfully sent an invitation link.']);
    }

    private static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}