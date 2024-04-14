<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;
use Illuminate\Support\Facades\File;

class SliderController extends Controller
{   
    protected $path = 'admin.pages.slider.';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sliders = Slider::all();
        return view($this->path.'slider-list',compact('sliders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->path.'slider-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validate($request,[

            'title'  => 'required|string|min:2|max:255',
            'description' => 'required|string|min:2|max:1000',
        ]);

        if($request->hasFile('image')) {
            
            $data['image'] = $request->image->store('sliders',['disk' => 'images']);

        }

        //create slider
        $slider = Slider::create($data);

        return redirect()->route('admin.sliders.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $slider = Slider::findorFail($id);
        return view($this->path.'slider-edit',compact('slider'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {   
        $slider = Slider::findorFail($id);
        $data = $this->validate($request,[

            'title'  => 'required|string|min:2|max:255',
            'description' => 'required|string|min:2|max:1000',
        ]);

        if($request->hasFile('image')) {

            $path = public_path('sliders/'.$slider->image);

            if (File::exists($path)) {
                File::delete($path);
            }
            
            $data['image'] = $request->image->store('sliders',['disk' => 'images']);

        }

        //update slider
        $slider->update($data);

        return redirect()->route('admin.sliders.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $slider = Slider::findorFail($id);
        $slider->delete();
        return redirect()->back();
    }

    public function slider_images() {

        $sliders = Slider::all();
        
        return response()->json($sliders);
    }
}
