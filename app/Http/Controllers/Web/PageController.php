<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\library\service;
use App\library\token;
use Auth;

class PageController extends Controller
{
	public function record(Request $request) {

		if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
		  return response('', 500)->header('Content-Type', 'text/plain');
		}

		$viewing = Service::loadModel('PageViewingHistory');
		$viewing->model = $request->model;
		$viewing->model_id = $request->modelId;
		$viewing->token = Token::generate(32);
		$viewing->page_id = $request->pageId;
		$viewing->user_id = Auth::user()->id;
		$viewing->save();

		return 1;
	}
}
