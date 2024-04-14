<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Traits\ApiResponser;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Carbon;
class BusinessController extends Controller
{
    use ApiResponser;
    protected $path = 'business.';


    public function __construct()
    {  
    
    }

    /**
     * **************************************************************
     *  Business-List
     * **************************************************************
     * */
    public function index()
    {

        $data = Business::latest()->get();
        return view($this->path . 'business-list');
    }



    /**
     * **************************************************************
     *  Business-Show
     * **************************************************************
     * */
    public function show(Business $business)
    {
        return view($this->path . 'business-show', compact('business'));
    }


    /**
     * **************************************************************
     *  Business-Change-Status
     * **************************************************************
     * */
    public function change_profile_verification_status(Request $request, $business_id)
    {

        $business = Business::findorFail($business_id);
        $business->is_verified = ($business->is_verified == 1) ? 0 : 1;
        if($business->is_verified == 1) {
            $business->verified_at = Carbon::now();
        }
        else {
            $business->verified_at = null;
        }
        $business->save();
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
    public function business_datatable()
    {

        $data = Business::latest()->get();
        $dataTable = DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('profile_image', function ($data) {
            return '<img src="'.asset('storage/avatar/'.$data->profile_image).'" width="100px"/>';
        })
        ->addColumn('profile_verified_html', function ($data) {

            $status = ($data->is_verified == '1') ? 'verified' : 'not verified';
            $class  = ($data->is_verified == '1') ? 'bg-gradient-success' : 'bg-gradient-danger';

            return '<span class="badge badge-sm swal_profile_verify_status_change ' . $class . '" style="cursor:pointer;" data-user-id="' . $data->id . '">' . $status . '</span>';
        })
        ->addColumn('action', function ($data) {
            $button = '';
            $button .= '<a class="btn btn-link text-dark px-2 mb-0 edit" href="' . route('admin.business.show', $data->id) . '"><i class="fas fa-solid fa-eye text-primary me-2" aria-hidden="true"></i></a>';
            return $button;
        })
        ->rawColumns(['action','profile_image','profile_verified_html'])
        ->make(true);

    return $dataTable;
    }
}

