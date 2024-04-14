<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class VendorController extends Controller
{
    protected $path = 'admin.pages.vendor.';
    public function __construct() {

    }
    
    public function index() {
        $users = User::where('role',User::VENDOR_ROLE)->get();
        return view($this->path.'vendor-list',compact('users'));
    }

    public function create(){
        return view($this->path.'vendor-create');
    }

    public function store(Request $request) {
        
        $data = $this->validate($request,[

            'name'  => 'required|string|min:2|max:255',
            'email' => 'required|string|email|min:5|max:255|unique:users',
            'password' => 'required|string|min:5|max:60|confirmed'
        ]);

        //create user
        $data['password'] = bcrypt($data['password']);
        $data['is_verified'] = User::VERIFIED_USER;
        $data['verification_token'] = NULL;
        $data['role'] = User::VENDOR_ROLE;
        $data['is_active'] = $request->has('active') ? User::ACTIVE_USER : User::INACTIVE_USER;

        $user = User::create($data);

        return redirect()->route('admin.vendors.index');
    }

    public function edit($id) {
          
        $user = User::findorFail($id);
        return view($this->path.'vendor-edit',compact('user'));
    }

    public function update(Request $request,$id) {
        
        $user = User::findorFail($id);
        $rules = [
            'name'  => 'required|string|min:2|max:255',
            'email' => 'required|string|email|min:5|max:255|unique:users,email,'.$user->id,
        ];

        if($request->has('password') && !empty($request->get('password'))) {
            $rules = array_merge($rules,['password' => 'required|string|min:5|max:60|confirmed']);
            
        }

        $data = $this->validate($request,$rules);

        $data['is_active'] = $request->has('active') ? User::ACTIVE_USER : User::INACTIVE_USER;
         
        $user->update($data);
        return redirect()->route('admin.vendors.index');
    }

    public function destroy($id) {
          
        $user = User::findorFail($id);
        $user->delete();
        return redirect()->back();
    }

    public function change_active_status(Request $request) {

        User::where('id',$request->get('user_id'))->update(['is_active' => $request->get('active')]);

        return response()->json(['message' => 'updated successfully!']);
    }
}
