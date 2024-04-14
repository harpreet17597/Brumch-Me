<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Jobs\SendQueryMailJob;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\ApiMethodsTrait;
use Exception;
use App\Traits\ApiResponser;
use App\Models\Query;
use Carbon\Carbon;

class QueryController extends Controller
{
    use ApiMethodsTrait, ApiResponser;

    /**
     * **************************************************************
     *  SEND QUERY
     * **************************************************************
    * */
    public function send_query(Request $request) {

        $this->validate($request,[

            'title' => 'required|string|min:2|max:1000',
            'query' => 'required|string|min:2|max:10000'
        ]);

        try {
            $user = auth()->user();
            if($user) {
                $query = Query::create([
                    'user_id' => $user->id,
                    'title'   => $request->get('title'),
                    'query'   => $request->get('query')
                ]);
                //Send Query
                dispatch(new SendQueryMailJob($query))->delay(Carbon::now()->addSeconds(5));
                return $this->returnSuccessResponse('Query submitted successfully!');
            }
            else {
                return $this->returnErrorResponse('User not found!');
            }

        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }
 
}
