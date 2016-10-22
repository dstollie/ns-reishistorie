<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;

class StoredJourneys extends Controller
{
	public function index()
	{

		$response = (new GuzzleClient())->get(
			config('sheetsu.api.url'),
			[
				'auth' => config('sheetsu.api.auth')
			]
		);

		$body = $response->getBody();

		return response()->json(json_decode($body));
	}

	public function store(Request $request)
	{
		$this->validate($request, [
			'toStation'   => 'required|string',
			'fromStation' => 'required|string',
			'dateTime'    => 'required'
		]);

		$body = json_encode($request->only([
			'fromStation', 'toStation', 'dateTime'
		]));

		$response = (new GuzzleClient())->post(
			config('sheetsu.api.url'),
			[
				'headers' => [
					'Content-Type' => 'application/json'
				],
				'auth' => config('sheetsu.api.auth'),
				'body' => $body
			]
		);

		$body = $response->getBody();

		return response()->json(json_decode($body));
	}

	public function remove(Request $request)
	{
		$this->validate($request, [
			'dateTime'    => 'required'
		]);

		$response = (new GuzzleClient())->delete(
			config('sheetsu.api.url') . '/dateTime/' . $request->input('dateTime'),
			[
				'auth' => config('sheetsu.api.auth')
			]
		);

		$body = $response->getBody();

		return response()->json(json_decode($body));
	}
}