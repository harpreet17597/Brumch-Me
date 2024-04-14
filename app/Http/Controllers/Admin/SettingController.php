<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Setting;

class SettingController extends Controller
{
    protected $path = 'admin.pages.setting.';
    public function __construct(){

    }

    public function changePassword() {
        return view($this->path.'change-password');
    }

    public function resetPassword(Request $request) {

        $this->validate($request,[

            'current_password'     => 'required|string|min:8|max:255',
            'new_password'         => 'required|string|min:8|max:255|different:current_password',
            'confirm_new_password' => 'required|string|min:8|max:255|same:new_password',
        ]);

        $user = Auth::user();
        //check current password
        if(Hash::check($request->get('current_password'),auth()->user()->password)) {
            //reset passoword
            $user->password = Hash::make($request->get('new_password'));
            $user->save();
            $message = 'The password is changed successfully!';
            return back()->withSuccess($message);

        } else {
            $error = ValidationException::withMessages([
                'current_password' => 'The current password is invalid'
            ]);
            throw $error;
        }
    }

    public function slider(Request $request) {

        if($request->isMethod("GET")) {
            $setting = Setting::first();
            return view($this->path.'slider',compact('setting'));
        }

        if($request->ajax()){

            $slider_title = $request->slider_title;
            $slider_description = $request->slider_description;

            $images = [];
            $data = [];
            if($request->hasFile('files')) {
                
                foreach($request->file('files') as $key=>$image) {
                    $path = $image->store('sliders',['disk' => 'images']);
                    array_push($images,$path);
                    $data[$key]['success'] = true;
                    $data[$key]['src'] = $path;
                }
            }

            $result = Setting::updateOrCreate([

                'slider_title' => $slider_title,
                'slider_description' => $slider_description,
                'slider_images' => json_encode($images)
            ]);

            return response()->json($data);
        }
    }
}
