<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use JWTAuth;
use Image;

class UserController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */

    public function updateUser(UpdateUserRequest $request) 
    {   
        $id = JWTAuth::user()->id;
        $MAX_IMAGE_HEIGHT = config('config.imageMaxDimension.height');
        $MAX_IMAGE_WIDTH = config('config.imageMaxDimension.width');
        $AVATAR_PATH = 'avatars/';

        $imageName = "";

        if ($request->avatar) {
             $imageName = $AVATAR_PATH.time().time().'.'.$request->avatar->extension();  
             $avatar = $request->file('avatar');
             $filePath = public_path('/');   

             $img = Image::make($avatar->path());
             $img->resize($MAX_IMAGE_HEIGHT, $MAX_IMAGE_WIDTH , function ($const) {
                $const->aspectRatio();
            })->save($filePath.'/'.$imageName);
        }
       
        $payload = [
            'name' => $request->name,
            'user_name' => $request->user_name,
            'password'  => bcrypt($request->password),
        ];

        if ($imageName !== "") {
            $payload['avatar'] = $imageName;
        }

        $user = User::where('id', $id)->update($payload);  

        if ($user) {
            return response()->json(['message' => 'Successfully updated your account']);
        } else {
            return response()->json(['message' => 'Encountered an error'], Response::HTTP_BAD_REQUEST);
        }
    }
}