<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Traits\ApiResponser;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Carbon;

class CustomerController extends Controller
{
    use ApiResponser;
    protected $path = 'customer.';


    public function __construct()
    {  
    
    }

    /**
     * **************************************************************
     *  Customer-List
     * **************************************************************
     * */
    public function index()
    {

        $data = Customer::latest()->get();
        return view($this->path . 'customer-list');
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
    public function change_profile_verification_status(Request $request, $customer_id)
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

        return redirect()->route('students.index')->with('success', 'Status updated successfully!');
    }

    /**
     * **************************************************************
     *  customer-Datatable
     * **************************************************************
     * */
    public function customer_datatable()
    {

        $data = Customer::latest()->get();
        $dataTable = DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('profile_image', function ($data) {
            return '<img src="'.asset('storage/avatar/'.$data->profile_image).'" width="100px"/>';
        })
        ->addColumn('action', function ($data) {
            $button = '';
            $button .= '<a class="btn btn-link text-dark px-2 mb-0 edit" href="' . route('admin.customer.show', $data->id) . '"><i class="fas fa-solid fa-eye text-primary me-2" aria-hidden="true"></i></a>';
            return $button;
        })
        ->rawColumns(['action','profile_image'])
        ->make(true);

    return $dataTable;
    }
}

