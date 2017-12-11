<?php

/**
 * @Author: ahmadnorin
 * @Date:   2017-11-28 00:12:29
 * @Last Modified by:   ahmadnorin
 * @Last Modified time: 2017-11-28 09:47:54
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ApiManager;
use Validator, Session, Redirect;

class ApiManagerController extends Controller
{
    public function index(Request $request)
    {
    	if($request->get('search') != '')
    	{
    	$data['data']		= ApiManager::where('client', 'like', '%'.$request->get('search').'%')
    						->orderBy('id', 'desc')
    						->paginate(env('PAGINATE', 10));

    	}
    	else
    	{
    		$data['data']		= ApiManager::orderBy('id', 'desc')->paginate(env('PAGINATE', 10));

    	}
    	return view('api_manager.index', $data);
    }

    public function create()
    {
    	return view('api_manager.create');
    }

    public function store(Request $request)
    {
    	$validator = Validator::make($request->all(), [
    		'client'			=> 'required|unique:api_manager,client',
    		'description'		=> 'required',
    		]);
    	if($validator->fails())
    	{
    		Session::flash('message', 'Please fix the error(s) below');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
    	}

      $token 		= $this->token();
    	$api = New ApiManager;
    	$api->client 			= str_replace(array('https://', 'http://'), array('',''),$request->input('client'));
      $api->api_keys 		= $token;
      $api->api_token 	= $token;
    	// $api->api_keys 			= $this->token();
    	$api->description 		= $request->input('description');
        $api->user_id           = 1;
    	$api->save();
    	Session::flash('message', 'Api Keys Data Saved Successfuly');
    	return Redirect::to('api_manager');
    }

    public function edit(Request $request, $id)
    {
    	$data['data']	= ApiManager::findOrFail($id);
    	return view('api_manager.edit', $data);
    }

    public function update(Request $request, $id)
    {
    	$validator = Validator::make($request->all(), [
    		'client'			=> 'required|unique:api_manager,client,'.$id,
    		'description'		=> 'required',
    		]);
    	if($validator->fails())
    	{
    		Session::flash('message', 'Please fix the error(s) below');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
    	}

    	$api = ApiManager::findOrFail($id);
        $api->client            = str_replace(array('https://', 'http://'),array('',''),$request->input('client'));
    	$api->description 		= $request->input('description');
    	$api->save();
    	Session::flash('message', 'Api Keys Data Update Successfuly');
    	return Redirect::to('api_manager');
    }

    public function token()
    {
    	    $length = 100;
		    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		    $charactersLength = strlen($characters);
		    $randomString = '';
		    for ($i = 0; $i < $length; $i++) {
		        $randomString .= $characters[rand(0, $charactersLength - 1)];
		    }
		    return $randomString;
    }
}
