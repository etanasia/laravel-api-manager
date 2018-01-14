<?php

/**
 * @Author: bantenprov
 * @Date:   2017-11-28 00:12:29
 * @Last Modified by:   bantenprov
 * @Last Modified time: 2017-11-28 09:47:54
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiKeys;
use Bantenprov\Workflow\Models\WorkflowModel;
use Bantenprov\Workflow\Models\WorkflowState;
use Bantenprov\Workflow\Models\WorkflowTransition;
use Bantenprov\Workflow\Models\History;
use That0n3guy\Transliteration;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7;

use Validator, Image, Session, File, Response, Redirect, Exception;
use Auth;

class ApiManagerController extends Controller
{
    public function index(Request $request)
    {
        try {
          if($request->get('search') != ''){
            $data['data']	= ApiKeys::with('getUserName')->where('client', 'like', '%'.$request->get('search').'%')
                                   ->orderBy('id', 'desc')
                                   ->paginate(env('PAGINATE', 10));
          } else{
            $data['data']	= ApiKeys::with('getUserName')->orderBy('id', 'desc')->paginate(env('PAGINATE', 10));
          }
        } catch (Exception $e) {
            $data['data']	= [];
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
        if(Auth::guest()){ $current_user = 1; }
        else{ $current_user = Auth::user()->id; }

        try {
            $token 		= $this->token();
          	$api = New ApiKeys;
          	$api->client 			= str_replace(array('https://', 'http://'), array('',''),$request->client);
          	$api->api_key 			= $token;
          	$api->description 		= $request->description;
            $api->user_id           = $current_user;

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
      				$this->saveHistory($api, $workflow->first(), $statesFrom->first(), $statesTo->first());

      				Session::flash('message', 'Api Keys Data Saved Successfuly');
      				return Redirect::to('api-manager');
      			}
        } catch (Exception $e) {
            Session::flash('message', 'Error 404 #error not found');
            return Redirect::to('api-manager');
        }
    }

    public function show($id)
    {
        try {
            $transition = WorkflowTransition::all();
            $data['transition'] = $transition;
            $history = History::with('getApiKeys')
                               ->with('getWorkflow')
                               ->with('getStateFrom')
                               ->with('getStateTo')
                               ->with('getUserName')
                               ->where('content_id', $id)
                               ->get();

            $data['history'] = $history;
            $data['id'] = $id;
            foreach ($history as $value) {
              $workstateto = $value->getStateTo->label;
            }
            $data['workflowstateto'] = $workstateto;
        		$data['data'] = ApiKeys::where('id', $id)->first();
        	  return view('api_manager.show', $data);
        } catch (Exception $e) {
            Session::flash('message', 'Error 404 #error not found');
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

        try {
            $api = ApiKeys::findOrFail($id);
            $api->client      = str_replace(array('https://', 'http://'),array('',''),$request->client);
          	$api->description = $request->description;
          	$api->save();
          	Session::flash('message', 'Api Keys Data Update Successfuly');
          	return Redirect::to('api-manager');
        } catch (Exception $e) {
            Session::flash('message', 'Error 404 #error not found');
            return Redirect::to('api-manager');
        }
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

    public function destroy($id)
    {
        try {
            ApiKeys::destroy($id);
          	Session::flash('message', 'Api Keys Data Deleted Successfuly');
          	return Redirect::to('api-manager');
        } catch (Exception $e) {
            Session::flash('message', 'Error 404 #error not found');
            return Redirect::to('api-manager');
        }
    }

    private function getWorkflow($model){
			$data = WorkflowModel::where('content_type', 'like', '%' . $model . '%');
      return $data;
    }

    private function getState($state){
      $name = \Transliteration::clean_filename(strtolower($state));
			$data = WorkflowState::where('status', 1)->where('name', 'like', '%' . $name . '%');
      return $data;
    }

    private function getHistory($content_id){
      $data = History::with('getApiKeys')
                         ->with('getWorkflow')
                         ->with('getStateFrom')
                         ->with('getStateTo')
                         ->with('getUserName')
                         ->where('content_id', $content_id);
      return $data;
    }

    private function saveHistory($api, $workflow, $statesFrom, $statesTo, $user_id = ""){
      if(Auth::guest()){ $current_user = 1; }
      else{
        if($user_id == ""){ $current_user = Auth::user()->id; }
        else { $current_user = $user_id; }
      }
    	$history = New History;
    	$history->content_id 			= $api->id;
    	$history->Workflow_id 			= $workflow->id;
      $history->from_state 			= $statesFrom->id;
    	$history->to_state 		= $statesTo->id;
      $history->user_id           = $current_user;
    	$history->save();
      return $history;
    }

    public function request(Request $request)
    {
    	$validator = Validator::make($request->all(), [
    		'client'			=> 'required',
    		'request'		=> 'required',
    		'deskripsi'		=> 'required',
    		'user_id'		=> 'required',
    		]);

    	if($validator->fails())
    	{
        return Response::json(array(
            'title' => 'Error',
            'type'  => 'error',
            'message' => $validator->errors()->all()
        ));
    	}

      try {
          $host 			= str_replace(array('https://', 'http://'), array('',''),$request->input('host'));
          $client 			= str_replace(array('https://', 'http://'), array('',''),$request->input('client'));
          $requests = ucwords($request->input('request'));
          $deskripsi = $request->input('deskripsi');
          $user_id = $request->input('user_id');
          $data = ApiKeys::where('client', 'like', '%' . $client . '%');
          if($data->count() == 0){
            $token 		= $this->token();
          	$api = New ApiKeys;
          	$api->client 			= $client;
          	$api->api_key 			= $token;
          	$api->description 		= $deskripsi;
            $api->user_id           = $user_id;

            //create history default
            $model = "ApiKeys";
            $fromState = "propose";
            $toState = "propose";
            $workflow = $this->getWorkflow($model);
            $statesFrom = $this->getState($fromState);
      			$statesTo = $this->getState($toState);
      			if($workflow->count() == 0){
                $error = true;
                $statusCode = 404;
                $title = 'Error';
                $type = 'error';
                $message = 'Error Workflow not found';
                $result = 'Not Found';
      			}
            elseif($statesTo->count() == 0 || $statesFrom->count() == 0){
                $error = true;
                $statusCode = 404;
                $title = 'Error';
                $type = 'error';
                $message = 'Error State not active or State not found';
                $result = 'Not Found';
      			}
            else{
        				$api->save();
        				$this->saveHistory($api, $workflow->first(), $statesFrom->first(), $statesTo->first(), $user_id);
                if(env('URL_APIMANAGER') != NULL){
                  $url_apimanager = str_replace('"', '',env('URL_APIMANAGER'));
                  if($url_apimanager != "" || $url_apimanager != NULL || $url_apimanager != false || !empty($url_apimanager)){
                    $transition = "Propose to Propose";
                    $this->send_apimanager($url_apimanager,$client,$host,$transition);
                  }
                }
                if($requests == 'Request'){
                  $model = "ApiKeys";
                  $fromState = "propose";
                  $toState = $requests;
                  $workflow = $this->getWorkflow($model);
                  $statesFrom = $this->getState($fromState);
            			$statesTo = $this->getState($toState);
            			if($workflow->count() == 0){
                      $error = true;
                      $statusCode = 404;
                      $title = 'Error';
                      $type = 'error';
                      $message = 'Error Workflow not found';
                      $result = 'Not Found';
            			}
                  elseif($statesTo->count() == 0 || $statesFrom->count() == 0){
                      $error = true;
                      $statusCode = 404;
                      $title = 'Error';
                      $type = 'error';
                      $message = 'Error State not active or State not found';
                      $result = 'Not Found';
            			}
                  else{
              				$this->saveHistory($api, $workflow->first(), $statesFrom->first(), $statesTo->first(), $user_id);
                      $error = false;
                      $statusCode = 200;
                      $title = 'Success';
                      $type = 'success';
                      $message = 'Data created successfully. Your request has already been send.';
                      $result = $request->all();
            			}
                }
                else{
                  $error = true;
                  $statusCode = 404;
                  $title = 'Error';
                  $type = 'error';
                  $message = 'Value Request must be Request.';
                  $result = $request->all();
                }
      			}
          }
          else {
            $get = $data->first();
            $history = $this->getHistory($get->id)->get();
            foreach ($history as $value) {
              $workstateto = $value->getStateTo->label;
            }
            if($workstateto == $requests){
              $error = true;
              $statusCode = 404;
              $title = 'Error';
              $type = 'error';
              $message = 'Data has already been taken.';
              $result = $request->all();
            }
            elseif($workstateto == 'Approved'){
              $error = true;
              $statusCode = 404;
              $title = 'Error';
              $type = 'error';
              $message = 'Data has already been Approved.';
              $result = $request->all();
            }
            elseif($workstateto == 'Rejected'){
              $error = true;
              $statusCode = 404;
              $title = 'Error';
              $type = 'error';
              $message = 'Data has already been Rejected.';
              $result = $request->all();
            }
            else {
              if($requests == 'Request'){
                $model = "ApiKeys";
                $fromState = "propose";
                $toState = $requests;
                $workflow = $this->getWorkflow($model);
                $statesFrom = $this->getState($fromState);
          			$statesTo = $this->getState($toState);
          			if($workflow->count() == 0){
                    $error = true;
                    $statusCode = 404;
                    $title = 'Error';
                    $type = 'error';
                    $message = 'Error Workflow not found';
                    $result = 'Not Found';
          			}
                elseif($statesTo->count() == 0 || $statesFrom->count() == 0){
                    $error = true;
                    $statusCode = 404;
                    $title = 'Error';
                    $type = 'error';
                    $message = 'Error State not active or State not found';
                    $result = 'Not Found';
          			}
                else{
            				$this->saveHistory($get, $workflow->first(), $statesFrom->first(), $statesTo->first(), $user_id);
                    $error = false;
                    $statusCode = 200;
                    $title = 'Success';
                    $type = 'success';
                    $message = 'Data created successfully. Your request has already been send.';
                    $result = $request->all();
          			}
              }
              else {
                $error = true;
                $statusCode = 404;
                $title = 'Error';
                $type = 'error';
                $message = 'Value Request must be Request.';
                $result = $request->all();
              }
            }
          }
      } catch (Exception $e) {
          $error = true;
          $statusCode = 404;
          $title = 'Error';
          $type = 'error';
          $message = 'Error';
          $result = 'Not Found';
      } finally {
          return Response::json(array(
            'error' => $error,
            'status' => $statusCode,
            'title' => $title,
            'type' => $type,
            'message' => $message,
            'result' => $result
          ));
      }
    }

    public function transition(Request $request)
    {
    	$validator = Validator::make($request->all(), [
    		'client'			=> 'required',
    		'request'		=> 'required',
    		]);

    	if($validator->fails())
    	{
        return Response::json(array(
            'title' => 'Error',
            'type'  => 'error',
            'message' => $validator->errors()->all()
        ));
    	}
      if(Auth::guest()){ $current_user = 1; }
      else{ $current_user = Auth::user()->id; }

      try {
          $client 			= str_replace(array('https://', 'http://'), array('',''),$request->input('client'));
          $host 			= str_replace(array('https://', 'http://'), array('',''),$request->input('host'));
          $requests = ucwords($request->input('request'));
          $data = ApiKeys::where('client', 'like', '%' . $client . '%');
          if($data->count() == 0){
            $token 		= $this->token();
          	$api = New ApiKeys;
          	$api->client 			= $client;
          	$api->api_key 			= $token;
          	$api->description 		= $requests;
            $api->user_id           = $current_user;

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
      			}
            elseif($statesTo->count() == 0 || $statesFrom->count() == 0){
      				Session::flash('message', 'Error 102 #error state not active or state not found');
      				return Redirect::to('api-manager');
      			}
            else{
        				$api->save();
        				$this->saveHistory($api, $workflow->first(), $statesFrom->first(), $statesTo->first());
                if(env('URL_APIMANAGER') != NULL){
                  $url_apimanager = str_replace('"', '',env('URL_APIMANAGER'));
                  if($url_apimanager != "" || $url_apimanager != NULL || $url_apimanager != false || !empty($url_apimanager)){
                    $transition = "Propose to Propose";
                    $this->send_apimanager($url_apimanager,$client,$host,$transition);
                  }
                }
                if($requests == 'Request'){
                  $model = "ApiKeys";
                  $fromState = "propose";
                  $toState = $requests;
                  $workflow = $this->getWorkflow($model);
                  $statesFrom = $this->getState($fromState);
            			$statesTo = $this->getState($toState);
            			if($workflow->count() == 0){
            				Session::flash('message', 'Error 101 #error workflow not found');
            				return Redirect::to('api-manager');
            			}
                  elseif($statesTo->count() == 0 || $statesFrom->count() == 0){
            				Session::flash('message', 'Error 102 #error state not active or state not found');
            				return Redirect::to('api-manager');
            			}
                  else{
              				$this->saveHistory($api, $workflow->first(), $statesFrom->first(), $statesTo->first());

              				Session::flash('message', 'Api Keys Data Saved Successfuly. Your request has already been send.');
              				return Redirect::to('api-manager');
            			}
                }
                else{
                    Session::flash('message', 'Error 404 #error not found');
                    return Redirect::to('api-manager');
                }
      			}
          }
          else {
            $get = $data->first();
            $history = $this->getHistory($get->id)->get();
            foreach ($history as $value) {
              $workstateto = $value->getStateTo->label;
            }
            if($workstateto == $requests){
              // kirim ke client
              $error = true;
              $statusCode = 404;
              $title = 'Error';
              $type = 'error';
              $message = 'Data has already been taken.';
              $result = $request->all();

      				Session::flash('message', 'Error 404 #error Data has already been taken.');
            }
            elseif($workstateto == 'Approved'){
              // kirim ke client
              $error = true;
              $statusCode = 404;
              $title = 'Error';
              $type = 'error';
              $message = 'Data has already been Approved.';
              $result = $request->all();

      				Session::flash('message', 'Error 101 #error Data has already been Approved.');
            }
            elseif($workstateto == 'Rejected'){
              // kirim ke client
              $error = true;
              $statusCode = 404;
              $title = 'Error';
              $type = 'error';
              $message = 'Data has already been Rejected.';
              $result = $request->all();

      				Session::flash('message', 'Error 101 #error Data has already been Rejected.');
            }
            else {
              $model = "ApiKeys";
              $fromState = $workstateto;
              $toState = $requests;
              $workflow = $this->getWorkflow($model);
              $statesFrom = $this->getState($fromState);
        			$statesTo = $this->getState($toState);
        			if($workflow->count() == 0){
                // kirim ke client
                $error = true;
                $statusCode = 404;
                $title = 'Error';
                $type = 'error';
                $message = 'workflow not found.';
                $result = $request->all();

        				Session::flash('message', 'Error 101 #error workflow not found');
        			}
              elseif($statesTo->count() == 0 || $statesFrom->count() == 0){
                // kirim ke client
                $error = true;
                $statusCode = 404;
                $title = 'Error';
                $type = 'error';
                $message = 'state not active or state not found.';
                $result = $request->all();

        				Session::flash('message', 'Error 102 #error state not active or state not found');
        			}
              else{
          				$this->saveHistory($get, $workflow->first(), $statesFrom->first(), $statesTo->first());
                  // kirim ke client
                  $error = false;
                  $statusCode = 200;
                  $title = 'Success';
                  $type = 'success';
                  $message = 'Data created successfully. Your request has already been send.';
                  $result = $get;

                  Session::flash('message', 'Api Keys Data Saved Successfuly. Your request has already been send.');
        			}
            }
            $history = $this->getHistory($get->id)->get();
            foreach ($history as $value) {
              $workstatefromid = $value->getStateFrom->id;
              $workstatetoid = $value->getStateTo->id;
              $workstatefrom = $value->getStateFrom->label;
              $workstateto = $value->getStateTo->label;
            }
            $state = $workstateto;
            $transition = $workstatefrom.' To '.$workstateto;
            $this->SendClient($client, $host, $error, $statusCode, $title, $type, $message, $result, $state, $transition);
            if(env('URL_APIMANAGER') != NULL){
              $url_apimanager = str_replace('"', '',env('URL_APIMANAGER'));
              if($url_apimanager != "" || $url_apimanager != NULL || $url_apimanager != false || !empty($url_apimanager)){
                $this->send_apimanager($url_apimanager,$client,$host,$transition);
              }
            }
            return Redirect::to('api-manager');
          }
      } catch (Exception $e) {
          Session::flash('message', 'Error 404 #error not found');
          return Redirect::to('api-manager');
      }
    }

    private function SendClient($client, $host, $error, $statusCode, $title, $type, $message, $result, $state, $transition){
        if(Auth::guest()){ $current_user = 1; }
        else{ $current_user = Auth::user()->id; }
        $headers = ['Content-Type' => 'application/json'];
        $data = [
          'error' => $error,
          'status' => $statusCode,
          'title' => $title,
          'type' => $type,
          'message' => $message,
          'result' => $result,
          'hostname' => $host,
          'keys' => $result->api_key,
          'state' => $state,
          'transition' => $transition,
          'user_id' => $current_user
        ];
        $body = json_encode($data);

        //kalo udah rilis
        $urlget = $client."/api/v1/host-keys/".$host."/get";

        //untuk local
        // $url = "bloger.local/api/v1/host-keys";

        $clients = new \GuzzleHttp\Client();
        $resget = $clients->request('GET', $urlget,['headers'=>$headers]);
        $responseget = $resget->getBody();
        $responsesget = json_decode($responseget);

        if($responsesget->result != 'Not Found'){
          $clients = new \GuzzleHttp\Client();
          $url = $client."/api/v1/host-keys/".$responsesget->id;
          $res = $clients->request('PUT', $url,['headers'=>$headers,'body'=>$body]);
          $response = $res->getBody();
          $responses = json_decode($response);
        }else {
          $clients = new \GuzzleHttp\Client();
          $url = $client."/api/v1/host-keys";
          $res = $clients->request('POST', $url,['headers'=>$headers,'body'=>$body]);
          $response = $res->getBody();
          $responses = json_decode($response);
        }
        return $responses;
    }

    private function send_apimanager($url_apimanager,$client,$host,$keterangan){
        if(Auth::guest()){ $current_user = 1; }
        else{ $current_user = Auth::user()->id; }
        $headers = ['Content-Type' => 'application/json'];
        $host 			= str_replace(array('https://', 'http://'), array('',''),$host);
        $client 			= str_replace(array('https://', 'http://'), array('',''),$client);
        $data = [
          'host' => $host,
          'client' => $client,
          'keterangan' => $keterangan,
          'user_id' => $current_user
        ];
        $body = json_encode($data);

        //kalo udah rilis
        $url = $url_apimanager."/api/store";

        //untuk local
        // $url = "bloger.local/api/v1/host-keys";

        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', $url,['headers'=>$headers,'body'=>$body]);
        $response = $res->getBody();
        $responses = json_decode($response);
        return $responses;
    }

    public function receive(Request $request){
        return Response::json(array(
          'error' => $request->error,
          'status' => $request->status,
          'title' => $request->title,
          'type' => $request->type,
          'message' => $request->message,
          'result' => $request->result
        ));
    }
}
