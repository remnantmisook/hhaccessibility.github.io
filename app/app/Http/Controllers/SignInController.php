<?php namespace App\Http\Controllers;

use App\User;
use App\BaseUser;
use App\AnswerRepository;
use Auth;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class SignInController extends Controller {
	
	public function showForm(Request $request)
	{
		$message = '';
		$email = '';
		if ( !empty(Input::get('email')) ) {
			$email = trim(Input::get('email'));
		}
		if ( !empty(Input::get('message')) ) {
			$message = trim(Input::get('message'));
		}
		return view('pages.signin', ['email' => $email,
		'confirmmessage' => '', 'message' => $message]);
	}

    /**
     * Handle an authentication attempt.
     *
     * @return Response
     */
    public function authenticate(Request $request)
    {
		$validation_rules = array(
			'email'            => 'required|email',
			'password'         => 'required',
		);
		$validator = Validator::make(Input::all(), $validation_rules);
		
		if (!$validator->fails())
		{
			$email = $request->input('email');
			if (BaseUser::authenticate($email, $request->input('password')))
			{
				//if(!BaseUser::checkEmail($email))
					//return view('pages.signin',['email' => $email,'confirmmessage'=>'A verification code has been sent to '.$email.'. Check your email to confirm.']);
				BaseUser::signIn($email);
				return redirect()->intended('profile');
			}
			else
			{
				$validator->errors()->add('password', 'Invalid email and password combination');
			}
		}
		
		return Redirect::to('signin')->withErrors($validator)->withInput();	
    }

	public function signout(Request $request)
	{
		BaseUser::signout();
		AnswerRepository::destroyUncommittedChanges();
		return redirect()->intended('/');
	}
}