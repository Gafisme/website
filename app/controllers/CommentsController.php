<?php

class CommentsController extends \BaseController {

	public function __construct()
	{
		$this->beforeFilter('auth', array('on' => 'store'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
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
			'content'  => Input::get('content'),
			'quote_id' => Input::get('quote_id'),
		];

		$validator = Validator::make($data, Comment::$rulesAdd);

		// Check if the form validates with success.
		if ($validator->passes()) {
			
			// Store the comment
			$comment = new Comment;
			$comment->content = $data['content'];
			$comment->quote_id = $data['quote_id'];
			$comment->user_id = Auth::user()->id;
			$comment->save();

			return Redirect::action('QuotesController@show',  ['id' => $data['quote_id']])->with('success', Lang::get('comments.commentAddedSuccessfull'));
		}

		// Something went wrong.
		return Redirect::action('QuotesController@show',  ['id' => $data['quote_id']])->withErrors($validator)->withInput(Input::all());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
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