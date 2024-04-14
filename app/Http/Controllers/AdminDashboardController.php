<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use DB;

class AdminDashboardController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');
  
    }

    public function adminIndex()
    {
        return view('backend.admin-home');
    }

   
    public function admin_profile_update(Request $request){
        $this->validate($request,[
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'description'=> 'nullable',
            'designation'=> 'nullable'
        ]);

        Admin::find(Auth::user()->id)->update([
            'name'=>$request->name,
            'email' => $request->email ,
            'description' => $request->description ,
            'designation' => $request->designation
        ]);
        return redirect()->route('admin.home')->with(['toastr_success_msg' => __('Profile Update Success' ), 'type' => 'success']);
    }

    public function admin_password_chagne(Request $request){
        $this->validate($request, [
            'old_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = Admin::findOrFail(Auth::guard('admin')->user()->id);

        if (Hash::check($request->old_password ,$user->password)){

            $user->password = Hash::make($request->password);
            $user->save();
            Auth::logout();

            return redirect()->route('admin.login')->with(['msg'=> __('Password Changed Successfully'),'type'=> 'success']);
        }

        return redirect()->back()->with(['msg'=> __('Somethings Going Wrong! Please Try Again or Check Your Old Password'),'type'=> 'danger']);
    }

    public function adminLogout(){

        Auth::guard('admin')->logout();
        return redirect()->route('admin.login')->with(['msg'=>__('You Logged Out !!'),'type'=> 'danger']);
    }

    public function admin_profile(){
        return view('auth.admin.edit-profile');
    }
    public function admin_password(){
        return view('auth.admin.change-password');
    }
}