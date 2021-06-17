<?php

namespace App\Http\Controllers;

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

    use \App\Traits\WebServicesDoc;
    /**
     * Get a token via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    
    public function login(Request $request) {

        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if($validator->fails()){
            return responseBuilder()->error(__($validator->errors()->first()), 400, false);
        }

        $credentials = request(['email', 'password']);

        $user = User::where('email',$credentials['email'])->first();

        if(!$user){
            return responseBuilder()->error(__('message.general.login_error'), 404, false);
        }
        if($user->email_verified == 0){
            return responseBuilder()->success(__('message.general.not_verified',["mod"=>"User"]), $user, false);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return responseBuilder()->error(__('message.general.login_error'), 404, false);
        }
        
        if (auth()->attempt($credentials)) {
   
            $user = auth()->user();
            $user['access_token'] = auth()->user()->createToken('user')->accessToken;
            $oResponse['user'] = $user;

            $oResponse = responseBuilder()->success(__('message.general.login',["mod"=>"User"]), $oResponse, true);
            $this->urlRec(0, 0, $oResponse);
            return $oResponse;  
            
        } else {
            return responseBuilder()->error(__('message.general.login_error'), 404, false);
        }
    }

    public function register(Request $request){

        $input = $request->all();

        $validator = Validator::make($input,[
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8'
        ]);

        if($validator->fails()){
            return responseBuilder()->error(__($validator->errors()->first()), 400, false);
        }

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

        $oResponse['user'] = $user;
        $oResponse = responseBuilder()->success(__('message.general.create',["mod"=>"User"]), $oResponse, true);
        $this->urlRec(0, 1, $oResponse);
        return $oResponse;
    }

    public function logout()
    {
        $user = Auth::user()->token();
        $user->revoke();
        $oResponse = responseBuilder()->success(__('message.general.logout'));
        $this->urlRec(0, 2, $oResponse);
        return $oResponse;
    }
    public function codeVerification(Request $request){
        
        $input = $request->all();

        $validator = Validator::make($input,[
            'code' => 'required|integer|min:6',
        ]);

        if($validator->fails()){
            return responseBuilder()->error(__($validator->errors()->first()), 400, false);
        }
        $current_time = Carbon::now()->toDateTimeString();
        $code_verification = EmailVerification::where('code',$input['code'])->first();
        $user = User::where('id',$code_verification->user_id)->first();
        
        if(!$code_verification){
            return responseBuilder()->error(__('message.general.notFind',['mod'=>'User']), 404, false);
        }
        if(!$user){
            return responseBuilder()->error(__('message.general.notFind',['mod'=>'User']), 404, false);
        }
        if($code_verification->is_used == 1){
            return responseBuilder()->error(__('message.general.already',['mod'=>'Code']), 404, false);
        }
        if($current_time > $code_verification->expire_at){
            return responseBuilder()->error(__('message.general.code_expired'), 404, false);
        }
        
        $code_verification->update(['is_used'=>1]);
        $user->update(['email_verified'=>1]);

        $user['access_token'] = $user->createToken('user')->accessToken;
        $oResponse['user'] = $user;

        $oResponse = responseBuilder()->success(__('message.general.verified',["mod"=>"User"]), $oResponse, true);
        $this->urlRec(0, 3, $oResponse);
        return $oResponse;  
    }

    public function reSendCode(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input,[
            'user_id' => 'required|exists:users,id',
        ]);

        if($validator->fails()){
            return responseBuilder()->error(__($validator->errors()->first()), 400, false);
        }
        $code = rand(100000,999999);
        $current_time = Carbon::now()->addMinutes(5)->toDateTimeString();
        
        $previous_code = EmailVerification::where('user_id',$input['user_id'])->first();
        if(!$previous_code){
            return responseBuilder()->error(__('message.general.notFind',['mod'=>'User']), 404, false);
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
        
        $oResponse = responseBuilder()->success(__('message.general.create',["mod"=>"Code"]),$user);
        $this->urlRec(0, 4, $oResponse);
        return $oResponse;
    }
}
