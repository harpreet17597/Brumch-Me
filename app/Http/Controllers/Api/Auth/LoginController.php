<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use ApiResponser;

    public function __construct() {

    }

    /**
     * **************************************************************
     *  LOGOUT START
     * **************************************************************
    * */
    public function logout(Request $request)
    {
        $userObj = $request->user();

        if (!$userObj) {
            return $this->notAuthorizedResponse('You are not authorized');
        }

        $userObj->tokens()->delete();
        // $userObj->fcm_token = null;
        $userObj->save();
        $userObj->setLoggedOut();
        return $this->returnSuccessResponse('User logged out successfully');
    }
    /**
     * **************************************************************
     *  LOGOUT END
     * **************************************************************
    * */
}
