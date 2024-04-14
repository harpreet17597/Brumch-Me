<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\CategoryProduct;

class ProductController extends Controller
{
    protected $path = 'admin.pages.product.';

    public function __construct(){
        $this->middleware('permission:product-list');
    }

    public function index()
    {
        $products = Product::all();
        return view($this->path.'product-list',compact('products'));
    }

    public function create()
    {
        $categories = Category::select('id','name')->get();
        return view($this->path.'product-create',compact('categories'));
    }

    public function store(Request $request)
    {
        $this->validate($request,[

            'name'  => 'required|string|min:2|max:255',
            'description' => 'required|string|min:2|max:1000',
            'size' => 'required|string',
            'quantity' => 'required|integer',
            'images.*' => 'image'
        ]);

        $data = [

            'name' => $request->name,
            'description' => $request->description,
            'size' => $request->size,
            'quantity' => $request->quantity
        ];

        //create product
        $product = Product::create($data);
        if($request->hasFile('images')) {
            foreach($request->file('images') as $image) {
                $imagename = $image->store('products',['disk' => 'images']);
                ProductImage::create(['image' => $imagename,'product_id' => $product->id]);
            }

        }

        if($request->has('categories')) {

            $product->categories()->attach($request->categories);
        }

        return redirect()->route('admin.product.index');
    }

    public function edit($id) {
        $categories = Category::select('id','name')->get();
        $product = Product::findorFail($id);
        return view($this->path.'product-edit',compact('product','categories'));
    }

    public function update(Request $request,$id) {

        $product = Product::findorFail($id);

        $this->validate($request,[

            'name'  => 'required|string|min:2|max:255',
            'description' => 'required|string|min:2|max:1000',
            'size' => 'required|string',
            'quantity' => 'required|integer',
            'image' => 'image'
        ]);

        $data = [

            'name' => $request->name,
            'description' => $request->description,
            'size' => $request->size,
            'quantity' => $request->quantity
        ];

        if($request->hasFile('images')) {
            foreach($request->file('images') as $image) {
                $imagename = $image->store('products',['disk' => 'images']);
                ProductImage::create(['image' => $imagename,'product_id' => $product->id]);
            }

        }

        //update product
        $product->update($data);

        if($request->has('categories')) {
            $product->categories()->sync($request->categories);
        }

        return redirect()->route('admin.product.index');
    }

    public function destroy($id){
        $product = Product::findorFail($id);
        ProductImage::where('product_id',$product->id)->delete();
        CategoryProduct::where('product_id',$product->id)->delete();
        $product->delete();

        return redirect()->route('admin.product.index');

    }

    public function product_images($id) {
        $product = Product::findorFail($id);

        return view($this->path.'product-images',compact('product'));
    }

    public function product_images_delete($product_id,$image_id) {
        $product = Product::findorFail($product_id);

        if($product) {
            $product_image = ProductImage::findorFail($image_id);

            $path = public_path($product_image->image);
            if (File::exists($path)) {
                File::delete($path);
            }

            $product_image->delete();
        }

        return redirect()->back();
    }
}
