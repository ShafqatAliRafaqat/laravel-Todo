<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
            return responseBuilder()->error(__('message.general.not_verified',["mod"=>"User"]), 401, false);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return responseBuilder()->error(__('message.general.login_error'), 404, false);
        }
        
        if (auth()->attempt($credentials)) {
   
            $oResponse['token'] = auth()->user()->createToken('user')->accessToken;
            $oResponse['user'] = auth()->user();

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
            'password' => 'required|string|min:6'
        ]);

        if($validator->fails()){
            return responseBuilder()->error(__($validator->errors()->first()), 400, false);
        }

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified' => $input['email_verified'],
            'password' => bcrypt($input['password'])
        ]);
        $users = User::where('id',$user->id)->first();
        $oResponse['token'] = $user->createToken('user')->accessToken;
        $oResponse['user'] = $users;
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
    public function deleteUser($id)
    {
        $user = User::findOrFail($id)->delete();
        $oResponse = responseBuilder()->success(__('message.general.delete',["mod"=>"User"]));
        $this->urlRec(0, 3, $oResponse);
        return $oResponse;
    }
    public function allUsers()
    {
        $users = User::all();
        $oResponse['users'] = $users;
        $oResponse = responseBuilder()->success(__('All User'), $oResponse, true);
        $this->urlRec(0, 4, $oResponse);
        return $oResponse;
    }
}
