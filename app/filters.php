<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});

Route::filter('can', function($route, $request, $value)
{
	if (Auth::user()->can($value)) {
		// User can view page
	} else {
		// User Shouldn't be here
		return Redirect::route('home')->withErrors(array('baduser' => 'You shouldn\'t go there. It\'s a naughty place.'));
	}
});



Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() !== Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

App::missing(function($exception)
{
	if (Config::get('app.debug')) {
    	return;
    }

    return Redirect::route('home')->withErrors("<strong>Error 404:</strong> The page you tried to navigate to does not exist!");
});

Route::filter('quote.owner', function ($route, $request) {
	$quote= $route->getParameter('quote');
	if (Auth::check()) {
		if ($quote->user->id == Auth::user()->id) return;
	}
	
	return Redirect::route('home')->withErrors(['e' => 'You are not the owner of quote #' . $quote->id]);
});

Route::filter('quote.pending', function ($route, $request) {
	$quote= $route->getParameter('quote');
	if ($quote->status <= 0) return;	
	return Redirect::route('home')->withErrors(['e' => 'This quote is not in a pending state and can not be edited']);
});

Route::filter('votes', function() {
	if (Auth::check()) {
		$rules = array(
			'upvote' => 'integer|numeric',
			'downvote' => 'integer|numeric',
			'unvote' => 'integer|numeric',
			);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->passes()) {
			$vote = NULL;
			$id = NULL;
			$type = NULL;
			if (Input::has('upvote')) {
				$type = 'upvote';
				$id = Input::get('upvote');
				$vote = 1;
			}
			if (Input::has('downvote')) {
				$type = 'downvote';
				$id = Input::get('downvote');
				$vote = 0;
			}
			if (Input::has('unvote')) {
				$type ='unvote';
				$id = Input::get('unvote');
			}

			$quote = Quote::find($id);
			if ($quote) {
				$vuser = $quote->voted()->whereUserId(Auth::user()->id)->first();
				if ($type == 'upvote' || $type == 'downvote') {
						if (!$vuser) {
							$quote->voted()->attach(Auth::user(), array('vote' => $vote));
						} else {
							$vuser->pivot->vote = $vote;
							$vuser->pivot->save();
							$quote->updateVoteConfidence();
						}
				} elseif ($type == 'unvote') {
					$quote->voted()->detach(Auth::user());
					$quote->updateVoteConfidence();
				}
				//Our confidence has changed in this quote.
				$quote->updateVoteConfidence();
			}
		}
	}
});


Route::filter('moderate', function() {
	if (Auth::check()) {
		$rules = array(
			'approve' => 'integer|numeric',
			'deny' => 'integer|numeric',
			);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->passes()) {
			if (Input::has('approve')) {
				$quote = Quote::find(Input::get('approve'));
				if ($quote) {
					if ($quote->status != 1) {
						$quote->status = 1;
						$quote->save();
						$response = Event::fire('quote.approved', array($quote));
					}
				}
			}
			if (Input::has('deny')) {
				$quote = Quote::find(Input::get('deny'));
				if ($quote) {
					if ($quote->status != -1) {
						$quote->status = -1;
						$quote->save();
					}
				}
			}
		}
	}
});


// Route::filter('favorite', function() {
// 	if (Auth::check()) {
// 		$rules = array(
// 			'favor' => 'integer|numeric',
// 			'unfavor' => 'integer|numeric'
// 		);
// 		$validator = Validator::make(Input::all(), $rules);
// 		if ($validator->passes()) {
// 			if (Input::has('favor')) {
// 				$id = Input::get('favor');
// 				$quote = Quote::find($id);
// 				if ($quote) {
// 					if (!Auth::user()->favorites->contains($id)) {
// 						Auth::user()->favorites()->attach($quote);
// 					}
// 				}
// 			}
// 			if (Input::has('unfavor')) {
// 				$id = Input::get('unfavor');
// 				$quote = Quote::find($id);
// 				if ($quote) {
// 					if (Auth::user()->favorites->contains($id)) {
// 						Auth::user()->favorites()->detach($quote);
// 					}
// 				}
// 			}
// 		}
// 	}
// });
