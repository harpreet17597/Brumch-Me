<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    protected $path = 'admin.pages.users.';
    public function __construct() {

    }
    
    public function index() {
        $users = User::all();
        return view($this->path.'user-list',compact('users'));
    }

    public function create(){
        return view($this->path.'user-create');
    }

    public function store(Request $request) {
        
        $data = $this->validate($request,[

            'name'  => 'required|string|min:2|max:255',
            'email' => 'required|string|email|min:5|max:255|unique:users',
            'password' => 'required|string|min:5|max:60|confirmed'
        ]);

        //create user
        $data['password'] = bcrypt($data['password']);
        $data['is_verified'] = '1';
        $data['verification_token'] = NULL;
        $data['role'] = 'customer';
        $data['is_suspended'] = $request->has('active') ? '1' : '0';
        $user = User::create($data);

        return redirect()->route('admin.users.index');
    }

    public function edit($id) {
          
        $user = User::findorFail($id);
        return view($this->path.'user-edit',compact('user'));
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

        $data['is_suspended'] = $request->has('active') ? '1' : '0';
         
        $user->update($data);
        return redirect()->route('admin.users.index');
    }

    public function destroy($id) {
          
        $user = User::findorFail($id);
        $user->delete();
        return redirect()->back();
    }

    public function change_active_status(Request $request) {

        User::where('id',$request->get('user_id'))->update(['is_suspended' => $request->get('active')]);

        return response()->json(['message' => 'updated successfully!']);
    }
}
