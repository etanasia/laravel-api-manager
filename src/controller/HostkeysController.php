<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Hostkeys;
use That0n3guy\Transliteration;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7;
use Auth;

use Validator, Image, Session, File, Response, Redirect, Exception;

class HostkeysController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
      try {
        if($request->get('search') != ''){
          $data['data']	= Hostkeys::with('getState')
                                 ->with('getTransition')
                                 ->with('getUserName')
                                 ->where('hostname', 'like', '%'.$request->get('search').'%')
                                 ->orderBy('id', 'desc')
                                 ->paginate(env('PAGINATE', 10));
        } else{
          $data['data']	= Hostkeys::with('getState')
                                 ->with('getTransition')
                                 ->with('getUserName')
                                 ->orderBy('id', 'desc')
                                 ->paginate(env('PAGINATE', 10));
        }
      } catch (Exception $e) {
          $data['data']	= [];
      }
      return view('host_keys.index', $data);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
    return view('host_keys.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request)
  {
      try {
          $token 		= $request->keys;
          $hostkey = New Hostkeys;
          $hostkey->hostname 			= str_replace(array('https://', 'http://'), array('',''),$request->hostname);
          $hostkey->keys 			= $token;
          $hostkey->state 		= $request->state;
          $hostkey->transition 		= $request->transition;
          $hostkey->user_id           = $request->user_id;
          if($request->state == 'Approved' || $request->state == 'approved' || $request->state == 'Rejected' || $request->state == 'rejected'){
            $hostkey->save();

            $error = false;
            $statusCode = 200;
            $title = 'Success';
            $type = 'success';
            $message = 'Data Saved Successfuly.';
            $result = $hostkey;
          }else {
              $error = true;
              $statusCode = 404;
              $title = 'Error';
              $type = 'error';
              $message = 'Error';
              $result = 'Not Found';
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

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {

  }

  public function get($hostname)
  {
      try {
          $data	= Hostkeys::with('getState')
                                 ->with('getTransition')
                                 ->with('getUserName')
                                 ->where('hostname', 'like', '%'.$hostname.'%')
                                 ->orderBy('id', 'desc')
                                 ->first();

           $error = false;
           $statusCode = 200;
           $title = 'Success';
           $type = 'success';
           $message = 'Success';
           $result = $data->hostname;
           $resultid = $data->id;
      } catch (Exception $e) {
          $error = true;
          $statusCode = 404;
          $title = 'Error';
          $type = 'error';
          $message = 'Error';
          $result = 'Not Found';
          $resultid = 'Not Found';
      } finally {
          return Response::json(array(
            'error' => $error,
            'status' => $statusCode,
            'title' => $title,
            'type' => $type,
            'message' => $message,
            'result' => $result,
            'id' => $resultid
          ));
      }
  }

  public function request(Request $request){
      $validator = Validator::make($request->all(), [
        'host'			=> 'required|unique:host_keys,hostname',
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
      $headers = ['Content-Type' => 'application/json'];
      $data = [
        'host' => $request->host,
        'client' => $request->client,
        'request' => 'Request',
        'deskripsi' => $request->description,
        'user_id' => $current_user
      ];
      $body = json_encode($data);

      //kalo udah rilis
      $host 			= str_replace(array('https://', 'http://'), array('',''),$request->host);
      $url = $host."/api/v1/api-manager/request";

      //untuk local
      // $url = "dashboard.local/api/v1/api-manager/receive";

      $client = new \GuzzleHttp\Client();
      $res = $client->request('POST', $url,['headers'=>$headers,'body'=>$body]);
      $response = $res->getBody();
      $responses = json_decode($response);

      $hostkey = New Hostkeys;
      $hostkey->hostname 			= $host;
      $hostkey->keys 			= "";
      $hostkey->state 		= $responses->result->request;
      $hostkey->transition 		= "Propose To ".$responses->result->request;
      $hostkey->user_id           = $responses->result->user_id;
      if($responses->status == 200){
        $hostkey->save();
        if(env('URL_APIMANAGER') != NULL){
          $url_apimanager = env('URL_APIMANAGER');
          if($url_apimanager != "" || $url_apimanager != NULL || $url_apimanager != false || !empty($url_apimanager)){
            $this->send_apimanager($url_apimanager,$request,$current_user,$hostkey->transition);
          }
        }
        Session::flash('message', 'Send Request Api Keys Successfuly');
      }else {
        Session::flash('message', $responses->message);
      }

      return Redirect::to('host-keys');
  }

  private function send_apimanager($url_apimanager,$request,$current_user,$keterangan){
      $headers = ['Content-Type' => 'application/json'];
      $data = [
        'host' => $request->host,
        'client' => $request->client,
        'keterangan' => $keterangan,
        'user_id' => $current_user
      ];
      $body = json_encode($data);

      //kalo udah rilis
      $url = $url_apimanager."/api/store";

      //untuk local
      // $url = "bloger.local/api/v1/host-keys";

      $client = new \GuzzleHttp\Client();
      $res = $client->request('POST', $url,['headers'=>$headers]);
      $response = $res->getBody();
      $responses = json_decode($response);
      return $responses;
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($id)
  {

  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
   public function update(Request $request, $id)
   {
       try {
           $token 		= $request->keys;
           $hostkey = Hostkeys::findOrFail($id);
           $hostkey->hostname 			= str_replace(array('https://', 'http://'), array('',''),$request->hostname);
           $hostkey->keys 			= $token;
           $hostkey->state 		= $request->state;
           $hostkey->transition 		= $request->transition;
           $hostkey->user_id           = $request->user_id;
           if($request->state == 'Approved' || $request->state == 'approved' || $request->state == 'Rejected' || $request->state == 'rejected'){
             $hostkey->save();

             $error = false;
             $statusCode = 200;
             $title = 'Success';
             $type = 'success';
             $message = 'Data Saved Successfuly.';
             $result = $hostkey;
           }else {
               $error = true;
               $statusCode = 404;
               $title = 'Error';
               $type = 'error';
               $message = 'Error';
               $result = 'Not Found';
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

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id)
  {

  }

}

?>
