<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;
use Illuminate\Support\Str;
use App\Helpers\FlashMsg;

class FrontendPageManageController extends Controller
{
    protected $path = 'backend.frontend-pages.';
    public function __construct()
    {  
    }

    /**
     * **************************************************************
     *  All-Pages-List
     * **************************************************************
    **/
    public function index(Request $request){
        $all_pages = Page::latest()->get();
        return view($this->path.'index')->with([
            'all_pages' => $all_pages,
        ]);
    }
    
    /**
     * **************************************************************
     *  Edit-Page
     * **************************************************************
    **/
    public function edit_page(Request $request,$id){
        $page_post = Page::find($id);
        return view($this->path.'edit')->with([
            'page_post' => $page_post
        ]);
    }
    
    /**
     * **************************************************************
     *  Update-Page
     * **************************************************************
    **/
    public function update_page(Request $request,$id){

        $this->validate($request,[
            'title' => 'required',
            'page_content' => 'nullable',
        ]);

        $page = Page::find($id);
            $page->title =  purify_html($request->title);
            $page->page_content =  $request->page_content;

        $slug = !empty($request->slug) ? $request->slug : Str::slug($request->title);
        $slug_check = Page::where(['slug' => $slug])->count();

        $slug = $slug_check > 1 ? $slug.'-5' : $slug;
        $page->slug = $slug;

        $page->save();
        return redirect()->route('admin.page')->with(FlashMsg::item_new('Page Updated Succefully'));
    }

}

