<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Traits\ApiResponser;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Carbon;

class FrontendCustomerManageController extends Controller
{
    use ApiResponser;
    protected $path = 'backend.frontend-customer.';


    public function __construct()
    {  
    
    }

    /**
     * **************************************************************
     *  Customer-List
     * **************************************************************
     * */
    public function all_customer()
    {
        $data = Customer::latest()->get();
        return view($this->path .'all-customer');
    }



    /**
     * **************************************************************
     *  Customer-Show
     * **************************************************************
     * */
    public function show(Customer $customer)
    {
        return view($this->path . 'customer-show', compact('customer'));
    }


    /**
     * **************************************************************
     *  Customer-Change-Status
     * **************************************************************
     * */
    public function customerVerify(Request $request, $customer_id)
    {

        $customer = Customer::findorFail($customer_id);
        $customer->is_verified = ($customer->is_verified == 1) ? 0 : 1;
        if($customer->is_verified == 1) {
            $customer->verified_at = Carbon::now();
        }
        else {
            $customer->verified_at = null;
        }
        $customer->save();
        if ($request->ajax()) {
            return $this->returnSuccessResponse('profile verification updated successfully!', $customer);
        }

        return redirect()->route('admin.all.frontend.customer')->with('success', 'Status updated successfully!');
    }

     /**
     * **************************************************************
     *  Customer-Account-Status
     * **************************************************************
     * */
    public function customerAccountStatus(Request $request, $customer_id)
    {

        $customer = Customer::findorFail($customer_id);
        $customer->is_suspended = ($customer->is_suspended == 1) ? 0 : 1;
        if($customer->is_suspended == 1) {
            $customer->suspended_at = Carbon::now();
        }
        else {
            $customer->suspended_at = null;
        }
        $customer->save();
        if ($request->ajax()) {
            return $this->returnSuccessResponse('Account status changed successfully', $customer);
        }

        return redirect()->route('admin.all.frontend.customer')->with('success', 'Account status changed successfully');
    }


    /**
     * **************************************************************
     *  Customer-Datatable
     * **************************************************************
     * */
    public function customer_datatable()
    {

        $data = Customer::latest()->get();
        $dataTable = DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('profile_image', function ($data) {
            return '<img src="'.$data->profile_image.'" width="100px"/>';
        })
        ->addColumn('account_status', function ($data) {
             
            $status = ($data->is_suspended == 0) ? 'Active' : 'Inactive';
            $class  = ($data->is_suspended == 0) ? 'bg-gradient-success' : 'bg-gradient-danger';
    
            return '<span class="text-info">'.$status.'</span><a tabindex="0" class="btn btn-warning btn-xs btn-sm mr-1 ml-3 swal_account_status_change" data-user-id="' . $data->id . '"><i class="ti-pencil"></i></a>';
        })
        ->addColumn('action', function ($data) {
            $button = '';
            $button .= '<a class="btn btn-link text-dark px-2 mb-0 edit" href="' . route('admin.customer.show', $data->id) . '"><i class="fas fa-solid fa-eye text-primary me-2" aria-hidden="true"></i></a>';
            return $button;
        })
        ->rawColumns(['action','account_status','profile_image'])
        ->make(true);

        return $dataTable;
    }
}

