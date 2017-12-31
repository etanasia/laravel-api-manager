<?php

namespace App\Http\Middleware;

use Closure;
use Redirect;
use Validator;
use App\ApiKeys;
use Bantenprov\Workflow\Models\History;

class ApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->header('apikey') == '')
        {
            return response()->json([
                'error' => true,
                'status' => 200,
                'title' => 'Error',
                'type' => 'error',
                'message' => 'apikey not found',
                'result' => []
            ]);
        }
        $check = ApiKeys::where('api_key', $request->header('apikey'))->first();
        if(count($check) == 0)
        {
            return response()->json([
                'error' => true,
                'status' => 200,
                'title' => 'Error',
                'type' => 'error',
                'message' => 'invalid apikey',
                'result' => []
            ]);
        }

        if(count($check) != 0){
          $history = History::with('getApiKeys')
                             ->with('getWorkflow')
                             ->with('getStateFrom')
                             ->with('getStateTo')
                             ->with('getUserName')
                             ->where('content_id', $check->id)->get();

         foreach ($history as $value) {
           $workstateto = $value->getStateTo->label;
         }
         if($workstateto == 'Propose'){
             return response()->json([
                 'error' => true,
                 'status' => 200,
                 'title' => 'Error',
                 'type' => 'error',
                 'message' => 'invalid apikey',
                 'result' => []
             ]);
         }
         elseif($workstateto == 'Request'){
             return response()->json([
                 'error' => true,
                 'status' => 200,
                 'title' => 'Error',
                 'type' => 'error',
                 'message' => 'invalid apikey',
                 'result' => []
             ]);
         }
         elseif($workstateto == 'Rejected'){
             return response()->json([
                 'error' => true,
                 'status' => 200,
                 'title' => 'Error',
                 'type' => 'error',
                 'message' => 'invalid apikey',
                 'result' => []
             ]);
         }
        }
        return $next($request);
    }
}
