<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Illuminate\Foundation\Validation\ValidatesRequests;

Route::get('/user', function (Request $request) {
	return $request->user();
})->middleware('auth:api');

Route::get('history', function () {
	$limit = request('limit');
	return response()->json(app('ns')->showHistory($limit));
});

Route::get('delayed-history', function () {
	$limit = request('limit');
	return response()->json(app('ns')->showDelayedHistory($limit));
});

Route::get('journey', function () {
	$url = request('url');

	return response()->json(app('ns')->submitMoneyBack($url));
});

Route::get('plan', function (Request $request) {

	$response = Guzzle::get(
		'http://webservices.ns.nl/ns-api-treinplanner',
		[
			'auth'  => config('ns.api.auth'),
			'query' => $request->all() + [
					'previousAdvices' => 0,
					'nextAdvices'     => 0
				]
		]
	);

	$body = $response->getBody();

	$xml = simplexml_load_string($body);
	$json = json_decode(json_encode($xml), true);


	return response()->json($json);
});

Route::group(['prefix' => 'stored-journeys'], function () {

	Route::get('/', 'Api\StoredJourneys@index');
	Route::post('/', 'Api\StoredJourneys@store');
	Route::post('/remove', 'Api\StoredJourneys@remove');

});