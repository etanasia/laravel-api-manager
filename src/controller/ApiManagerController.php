<?php

/**
 * @Author: bantenprov
 * @Date:   2017-11-28 00:12:29
 * @Last Modified by:   bantenprov
 * @Last Modified time: 2017-11-28 09:47:54
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ApiKeys;
use Bantenprov\Workflow\Models\WorkflowModel;
use Bantenprov\Workflow\Models\WorkflowState;
use Bantenprov\Workflow\Models\WorkflowTransition;
use Bantenprov\Workflow\Models\History;
use That0n3guy\Transliteration;
use Validator, Session, Redirect;

class ApiManagerController extends Controller
{
    public function index(Request $request)
    {
    	if($request->get('search') != '')
    	{
    	$data['data']		= ApiKeys::where('client', 'like', '%'.$request->get('search').'%')
    						->orderBy('id', 'desc')
    						->paginate(env('PAGINATE', 10));
    	}
    	else
    	{
    		$data['data']		= ApiKeys::orderBy('id', 'desc')->paginate(env('PAGINATE', 10));
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
    		'client'			=> 'required|unique:api_keys,client',
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
    	$api = New ApiKeys;
    	$api->client 			= str_replace(array('https://', 'http://'), array('',''),$request->input('client'));
    	$api->api_key 			= $token;
    	$api->description 		= $request->input('description');
      $api->user_id           = 1;
    	

      //create history default
      $model = "ApiKeys";
      $fromState = "propose";
      $toState = "propose";
      $workflow = $this->getWorkflow($model);
      $statesFrom = $this->getState($fromState);
			$statesTo = $this->getState($toState);
			if($workflow->count() == 0){
				Session::flash('message', 'Error 101 #error workflow not found');
				return Redirect::to('api-manager'); 
			}elseif($statesTo->count() == 0 || $statesFrom->count() == 0){
				Session::flash('message', 'Error 102 #error state not active or state not found');
				return Redirect::to('api-manager');
			}else{				
				$api->save();
				$this->saveHistory($api, $workflow->get(), $statesFrom->get(), $statesTo->get());

				Session::flash('message', 'Api Keys Data Saved Successfuly');
				return Redirect::to('api-manager');
			}
      
    }

    public function edit(Request $request, $id)
    {
    	$data['data']	= ApiKeys::findOrFail($id);
    	return view('api_manager.edit', $data);
    }

    public function update(Request $request, $id)
    {
    	$validator = Validator::make($request->all(), [
    		'client'			=> 'required|unique:api_keys,client,'.$id,
    		'description'		=> 'required',
    		]);
    	if($validator->fails())
    	{
    		Session::flash('message', 'Please fix the error(s) below');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
    	}

    	$api = ApiKeys::findOrFail($id);
      $api->client            = str_replace(array('https://', 'http://'),array('',''),$request->input('client'));
    	$api->description 		= $request->input('description');
    	$api->save();
    	Session::flash('message', 'Api Keys Data Update Successfuly');
    	return Redirect::to('api-manager');
    }

    public function token()
    {
  	    $length = 70;
		    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		    $charactersLength = strlen($characters);
		    $randomString = '';
		    for ($i = 0; $i < $length; $i++) {
		        $randomString .= $characters[rand(0, $charactersLength - 1)];
		    }
		    return $randomString;
    }

    private function getWorkflow($model){
			//$data = WorkflowModel::where('content_type', 'like', '%' . $model . '%')->get();
			$data = WorkflowModel::where('content_type', 'like', '%' . $model . '%');
      return $data;
    }

    private function getState($state){
      $name = \Transliteration::clean_filename(strtolower($state));
			//$data = WorkflowState::where('status', 1)->where('name', 'like', '%' . $name . '%')->get();
			$data = WorkflowState::where('status', 1)->where('name', 'like', '%' . $name . '%');
      return $data;
    }

    private function saveHistory($api, $workflow, $statesFrom, $statesTo){
    	$history = New History;
    	$history->content_id 			= $api->id;
    	$history->Workflow_id 			= $workflow[0]->id;
      $history->from_state 			= $statesFrom[0]->id;
    	$history->to_state 		= $statesTo[0]->id;
      $history->user_id           = 1;
    	$history->save();
      return $history;
    }
}
