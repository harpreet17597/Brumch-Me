<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiMethodsTrait;
use Exception;
use App\Traits\ApiResponser;
use App\Models\Tag;

class TagController extends Controller
{
    use ApiMethodsTrait, ApiResponser;

    public function __construct() {

    }

    /**
     * **************************************************************
     *  TAGS START
     * **************************************************************
    * */
    public function tags(Request $request)
    {
        try {
            $records = Tag::all();
            return $this->returnSuccessResponse('Tags list',$records);
        }
        catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }

}
