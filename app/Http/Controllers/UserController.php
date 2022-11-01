<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use DB;
use Hash;
use Exception;
use Validator;
use Auth;
use Session;


class UserController extends Controller
{

   public function signup()
   {
    return view('sigup');
}

public function signuppost(Request $request){

    try{
       $input = $request->all();
       $rules = [
        "name"=>"required",
        "email"=>"required",
        "password"=>"required",
        "usertype"=>"required"
    ];
    $validation = Validator::make($input, $rules);
    if($validation->fails()){
        return redirect()->back()->withErrors($validation)->with('validation', 'validation');
    }
    $query = new User();
    $query->userId = User::max('id')+1;
    $query->name = $input['name'];
    $query->email  = $input['email'];
    $query->password = Hash::make($input['password']);
     $query->remember_token = Hash::make($input['password']);
    $query->userType = $input['usertype'];
    $query->privilege = $input['usertype'];
    $query->status = 'active';
    $query->save();
    return redirect()->back()->with(['status'=>true]);

}catch (Exception $e) {
    return redirect()->back()->with(['error' => $e->getMessage().' at Line '.$e->getLine()]);
}

}


public function login()
{
   return view('signin');
}

public function loginpost(Request $request)
{
   try {
      $input = $request->all();
      $rules = [
         "username"=>"required",
         "password"=>"required"
     ];
     $validation = Validator::make($input, $rules);
     if($validation->fails()){
         return redirect()->back()->withErrors($validation)->with('validation', 'validation');
     }

     if(Auth::attempt(array('email' => $input['username'], 'password' => $input['password']))){
         return redirect()->route('home');    		
     }else{
         return redirect()->back()->with(['error' => 'Login Failed!, Check user name and password']);
     }
 } catch (Exception $e) {
  return redirect()->back()->with(['error' => $e->getMessage().' at Line '.$e->getLine()]);
}
}

public function homepage()
{
   if (Auth::check()) {
    return view('welcome');
}
return redirect()->route('login');
}

public function logout()
{
  Auth::logout();
  Session::flush();
  return redirect()->route('login');
}
}
