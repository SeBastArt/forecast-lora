<?php


namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

  //todo test for security
  public function __construct()
  {
    //$this->middleware('api');
  }

  protected $successStatus = 200;

  // login api
  public function login()
  {
    if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
      $user = Auth::user();
      $success['token_plain'] = $user->createToken('LoginToken')->plainTextToken;
      $success['user_name'] = $user->name;
      return response()->json(['success' => $success], $this->successStatus);
    } else {
      return response()->json(['error' => 'Unauthorised'], 401);
    }
  }

  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'email' => 'required|email',
      'password' => 'required',
      'c_password' => 'required|same:password',
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()], 401);
    }

    $input = $request->all();
    $input['password'] = bcrypt($input['password']);
    $user = User::create($input);
    $success['token'] =  $user->createToken('Personal Access Token')->accessToken;
    $success['name'] =  $user->name;
    return response()->json(['success' => $success], $this->successStatus);
  }


  // user logout api
  public function logout()
  {
    $user = Auth::user();
    return response()->json(['success' => 'Successfully Logout!']);
  }
}
