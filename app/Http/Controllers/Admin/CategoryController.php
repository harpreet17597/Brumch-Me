<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{   
    protected $path = 'admin.pages.category.';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        return view($this->path.'category-list',compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->path.'category-create');
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

            'name'  => 'required|string|min:2|max:255',
            'description' => 'required|string|min:2|max:1000',
        ]);

        if($request->hasFile('image')) {
            
            $data['image'] = $request->image->store('categories',['disk' => 'images']);

        }

        //create category
        $category = Category::create($data);

        return redirect()->route('admin.category.index');
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
        $category = Category::findorFail($id);
        return view($this->path.'category-edit',compact('category'));
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
        $category = Category::findorFail($id);
        $data = $this->validate($request,[

            'name'  => 'required|string|min:2|max:255',
            'description' => 'required|string|min:2|max:1000',
        ]);

        if($request->hasFile('image')) {

            $path = public_path('categories/'.$category->image);

            if (File::exists($path)) {
                File::delete($path);
            }
            
            $data['image'] = $request->image->store('categories',['disk' => 'images']);

        }

        //update category
        $category->update($data);

        return redirect()->route('admin.category.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findorFail($id);
        $category->delete();
        return redirect()->back();
    }
}
