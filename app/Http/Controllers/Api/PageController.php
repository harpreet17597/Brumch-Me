<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiMethodsTrait;
use Exception;
use App\Traits\ApiResponser;
use App\Models\Page;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    use ApiMethodsTrait, ApiResponser;

    public function __construct() {

    }

    /**
     * **************************************************************
     *  Page Detail
     * **************************************************************
    * */
    public function get_page_detail(Request $request,$page_slug)
    {
        try {
            $page = Page::select('title','slug','page_content')->where('slug',$page_slug)->first();
            if($page) {
                return $this->returnSuccessResponse('Tags list',$page);
            }
            return $this->returnErrorResponse('page not found!');
        }
        catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }

}
