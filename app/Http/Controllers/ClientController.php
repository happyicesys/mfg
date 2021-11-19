<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    public $successStatus = 200;
    // login api
    public function login(){
        $email = request('email');

        $field = filter_var($email,FILTER_VALIDATE_EMAIL)? 'email': 'phoneNumber';
          if(Auth::attempt([$field => request('email'), 'password' => request('password')])){
              $user = Auth::user();
              $success['token'] =  $user->createToken('MyApp')-> accessToken;
              return response()->json(['success' => $success,'user'=>$user], $this->successStatus);
          }
          else{
              return response()->json(['error'=>'Unauthorised'], 401);
          }
      }
}
