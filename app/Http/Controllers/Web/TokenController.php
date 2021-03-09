<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class TokenController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {

        //user allowed?
        $response = Gate::inspect('create', Token::class);
        if (!$response->allowed()) {
            //create errror message
            return redirect(
                action(
                    'Web\UserController@show',
                    ['user' => $user->id]
                )
            )
                ->withErrors([$response->message()]);
        }

        //Validation 
        $request->validate([
            'token_name' => 'required|min:5|max:255',
            'token_ability' => 'required',
        ]);

            
        Session::flash('message', "Token \"" . $user->createToken($request->token_name, [$request->token_ability])->plainTextToken . "\" created");
        return Redirect::back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, $tokenId)
    {
        $response = Gate::inspect('forceDelete', Token::class);
        if (!$response->allowed()) {
            //create errror message
            return redirect(
                action(
                    'Web\UserController@show',
                    ['user' => $user->id]
                )
            )
                ->withErrors([$response->message()]);
        }
        $user->tokens()->where('id', $tokenId)->delete();
    }
}
