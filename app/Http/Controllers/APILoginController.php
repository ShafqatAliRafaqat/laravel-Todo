<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserTokenResource;
use App\Mail\SendCode;
use App\Models\EmailVerification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Mail;

class APILoginController extends Controller {

    /**
     * Get a token via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    
    public function login(LoginRequest $request) {

        $credentials = request(['email', 'password']);

        $user = User::where('email',$credentials['email'])->first();

        if(!$user){
            abort(404,__('message.general.notFind',["mod"=>"User"]));
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            abort(401,__('message.general.login_error'));
        }
        if($user->email_verified == 0){
            abort(403,__('message.general.not_verified',["mod"=>"User"],$user));
        }
        if (auth()->attempt($credentials)) {
   
            $token = $user->createToken('user')->accessToken;
            $user->token = $token;
            return [
                'message'=>__('message.general.login'),
                'data' => UserTokenResource::make($user)->toArray($request),
            ];            
        } else {
            abort(401,__('message.general.login_error'));
        }
    }

    public function register(RegisterRequest $request){

        $input = $request->all();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified' => 0,
            'password' => bcrypt($input['password'])
        ]);

        $code = rand(100000,999999);
        $current_time = Carbon::now()->addMinutes(5)->toDateTimeString();

        EmailVerification::create([
            'user_id' => $user->id,
            'code' => $code,
            'is_used' => 0,
            'expire_at' => $current_time,
        ]);
        
        $user['code'] = $code;
        // Mail::to($user->email)->send(new SendCode($user));

        return [
            'message'=>__('message.general.create',['mod'=>'User']),
            'data' => UserTokenResource::make($user)->toArray($request),
        ];
    }

    public function logout()
    {
        $user = Auth::user()->token();
        $user->revoke();
        return response()->json(['message' => __('message.general.logout')]);
    }
    public function codeVerification(Request $request){
        
        $input = $request->all();

        $validator = Validator::make($input,[
            'code' => 'required|integer|min:6',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }
        $current_time = Carbon::now()->toDateTimeString();
        $code_verification = EmailVerification::where('code',$input['code'])->first();
        $user = User::where('id',$code_verification->user_id)->first();
        
        if(!$code_verification){
            abort(404,__('message.general.notFind',["mod"=>"User"]));
        }
        if(!$user){
            abort(404,__('message.general.notFind',["mod"=>"User"]));
        }
        if($code_verification->is_used == 1){
            abort(404,__('message.general.already',["mod"=>"Code"]));
        }
        if($current_time > $code_verification->expire_at){
            abort(404,__('message.general.code_expired'));
        }
        
        $code_verification->update(['is_used'=>1]);
        $user->update(['email_verified'=>1]);

        $token = $user->createToken('user')->accessToken;
        $user->token = $token;
        return [
            'message'=>__('message.general.login'),
            'data' => UserTokenResource::make($user)->toArray($request),
        ];
    }

    public function reSendCode(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input,[
            'user_id' => 'required|exists:users,id',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }
        $code = rand(100000,999999);
        $current_time = Carbon::now()->addMinutes(5)->toDateTimeString();
        
        $previous_code = EmailVerification::where('user_id',$input['user_id'])->first();
        if(!$previous_code){
            abort(404,__('message.general.notFind',["mod"=>"User"]));
        }
        
        $previous_code->update(['is_used'=>1]);
        
        EmailVerification::create([
            'user_id' => $input['user_id'],
            'code' => $code,
            'is_used' => 0,
            'expire_at' => $current_time,
        ]);
        
        $user = User::where('id',$input['user_id'])->first();

        $user['code'] = $code;
        // Mail::to($user->email)->send(new SendCode($user));
        return [
            'message'=>__('message.general.create',["mod"=>"Code"]),
            'data' => $user,
        ];
    }
}
