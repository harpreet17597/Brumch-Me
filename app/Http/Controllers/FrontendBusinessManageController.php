<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\User;
use App\Traits\ApiResponser;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use App\Helpers\CommonHelper;

class FrontendBusinessManageController extends Controller
{
    use ApiResponser;
    protected $path = 'backend.frontend-business.';


    public function __construct()
    {  
    
    }

    /**
     * **************************************************************
     *  Business-List
     * **************************************************************
     * */
    public function all_business()
    {
        return view($this->path .'all-business');
    }

     /**
     * **************************************************************
     *  Featured-Business-List
     * **************************************************************
     * */
    public function all_featured_business()
    {
        return view($this->path .'all-featued-business');
    }


    /**
     * **************************************************************
     *  Business-Show
     * **************************************************************
     * */
    public function businessProfileView($business_id)
    {
        $business_details = Business::where('id',$business_id)->with('restaurant')->first();
        if($business_details) {
            return view($this->path . 'business-details')->with(['business_details' => $business_details]);
        }

        abort(404);
    }


    /**
     * **************************************************************
     *  Business-Change-Status
     * **************************************************************
     * */
    public function businessVerify(Request $request, $business_id)
    {

        $business = User::findorFail($business_id);
        $business->is_verified = ($business->is_verified == 1) ? 0 : 1;
        if($business->is_verified == 1) {
            $business->verified_at = Carbon::now();
            $notificationMessage = "Your business profile is approved by admin."; 
        }
        else {
            $notificationMessage = "Your business profile is disapproved by admin.";
            Sanctum::actingAs($business)->tokens()->delete();
            $business->verified_at = null;
        }
        if($business->save()) {
            
            if($business->isLoggedIn() == '1') {
                    
                //notify the connection request recevier/profile.
                if (filled($business->fcm_token)) {
    
                   
                    $additionalData = [
                        'user_id'          => $business->id,
                        'title'            => ($business->is_verified == 1) ? 'Profile Approved' : 'Profile Disapproved',
                        'body'             => $notificationMessage,
                        'type'             => 1,
                        'role'             => 'business',
                        'notificationType' => 'business_approval',
                    ];
    
                    CommonHelper::sendCurlPushNotification($business->fcm_token, $additionalData);
                }
            }

        }
        if ($request->ajax()) {
            return $this->returnSuccessResponse('profile verification updated successfully!', $business);
        }

        return redirect()->route('students.index')->with('success', 'Status updated successfully!');
    }

    /**
     * **************************************************************
     *  Business-Datatable
     * **************************************************************
     * */
    public function business_datatable(Request $request)
    {

        $data = Business::latest();
        $data = $data->get();
        $dataTable = DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('profile_image', function ($data) {
            return '<img src="'.$data->profile_image.'" width="100px"/>';
        })
        ->addColumn('profile_verified_html', function ($data) {

            $status = ($data->is_verified == '1') ? 'verified' : 'not verified';
            $class  = ($data->is_verified == '1') ? 'bg-gradient-success' : 'bg-gradient-danger';

            return '<a tabindex="0" class="btn btn-warning btn-xs btn-sm mr-1 ml-3 swal_profile_verify_status_change edt" data-user-id="' . $data->id . '"><i class="ti-pencil"></i></a> <span class="text-info t-span">'.$status.'</span>';
        })
        ->addColumn('action', function ($data) {
            $button = '';
            $button .= '<a class="btn btn-link text-dark px-2 mb-0 edit" href="' . route('admin.frontend.business.profile.view', $data->id) . '"><i class="fas fa-solid fa-eye text-primary me-2" aria-hidden="true"></i></a>';
            return $button;
        })
        ->rawColumns(['action','profile_image','profile_verified_html'])
        ->make(true);

        return $dataTable;
    }
    
    /**
     * **************************************************************
     *  Featured-Business-Datatable
     * **************************************************************
     * */
    public function featured_business_datatable(Request $request) {

        $data = Business::latest();

        $data =  $data->whereHas('featured_subscription_status',function($query) {
            return $query->where('status','active');
        })->with('featured_subscription_status');
        
        $data = $data->get();
        $dataTable = DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('profile_image', function ($data) {
            return '<img src="'.$data->profile_image.'" width="100px"/>';
        })
        ->addColumn('action', function ($data) {
            $button = '';
            $button .= '<a class="btn btn-link text-dark px-2 mb-0 edit" href="' . route('admin.frontend.business.profile.view', $data->id) . '"><i class="fas fa-solid fa-eye text-primary me-2" aria-hidden="true"></i></a>';
            return $button;
        })
        ->rawColumns(['action','profile_image'])
        ->make(true);

        return $dataTable;
    }
}

