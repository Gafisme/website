<?php

class QuotesController extends \BaseController {

	public function __construct()
	{
		$this->beforeFilter('auth', array('on' => 'store'));
	}

	/**
	 * Display a bunch of quotes
	 *
	 * @return Response
	 */
	public function index()
	{
		// Random quotes or not?
		if (Route::currentRouteName() != 'random')
			$quotes = Quote::published()->with('user')->orderBy('created_at', 'DESC')->paginate(10);
		else
			$quotes = Quote::published()->with('user')->orderBy(DB::raw('RAND()'))->paginate(10);

		// Handle not found
		if (is_null($quotes))
			return Response::view('errors.missing', array(), 404);

		$data = [
			'quotes'          => $quotes,
			'colors'          => Quote::getRandomColors(),
			'pageTitle'       => Lang::get('quotes.'.Route::currentRouteName().'PageTitle'),
			'pageDescription' => Lang::get('quotes.'.Route::currentRouteName().'PageDescription'),
		];

		return View::make('quotes.index', $data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$data = [
			'content'    => Input::get('content'),
		];

		$validator = Validator::make($data, Quote::$rulesAdd);

		// Check if the form validates with success.
		if ($validator->passes()) {
			
			// Store the quote
			$quote = new Quote;
			$quote->content = $data['content'];

			$user = Auth::user();
			$login = $user->login;
			$user->quotes()->save($quote);

			return Redirect::route('home')->with('success', Lang::get('quotes.quoteAddedSuccessfull', array('login' => $login)));
		}

		// Something went wrong.
		return Redirect::route('addquote')->withErrors($validator)->withInput(Input::all());
	}

	/**
	 * Display the form to add a quote
	 *
	 * @return Response
	 */
	public function getAddQuote()
	{
		$data = [
			'pageTitle'       => Lang::get('quotes.addquotePageTitle'),
			'pageDescription' => Lang::get('quotes.addquotePageDescription'),
		];

		return View::make('quotes.addquote', $data);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{

		$quote = Quote::find($id);
		
		// Handle not found
		if (is_null($quote))
			return Response::view('errors.missing', array(), 404);

		$data = [
			'quote'  => $quote,
			'colors' => Quote::getRandomColors(),
			'comments' => Comment::with('user')->where('quote_id', '=', $quote->id)->orderBy('created_at', 'asc')->get(),
		];

		return View::make('quotes.show', $data);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}