<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Hostkeys;
use That0n3guy\Transliteration;
use Validator, Image, Session, File, Response, Redirect, Exception;

class HostkeysController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
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
          if($request->state == 'Approved' || $request->state == 'Rejected'){
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
  public function update($id)
  {

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
