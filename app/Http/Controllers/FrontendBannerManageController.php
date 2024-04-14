<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\CommonHelper;
use App\Models\BannerImage;
use App\Traits\ApiMethodsTrait;
use Exception;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\File;

class FrontendBannerManageController extends Controller
{
    use ApiMethodsTrait, ApiResponser;
    protected $path = 'backend.frontend-banner.';

    public function all_banner()
    {
        $banners = BannerImage::all();
        return view($this->path . 'all-banner',compact('banners'));
    }

    public function upload_banner_image(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|in:1,2,3',
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg', 'max:100000',
        ]);
        try {
            $banner = BannerImage::find($request->get('id'));
            if (!$banner) {
                $banner = new BannerImage();
            }else {
                 /*check file exist*/
                 $arr = explode('/', $banner->banner_image);
                 $filename = end($arr);
                 $file_path = 'uploads/banner/' . $filename;
                 $file_path = public_path($file_path);
                 if (File::exists($file_path)) {
                     File::delete($file_path);
                 }
            }
            $banner->id = $request->get('id');
            $path = CommonHelper::uploadImage('banner/', $request->file('file'));
            $banner->banner_image = $path;
            if ($banner->save()) {
                return $this->returnSuccessResponse('Banner Uploaded Successfully!', $banner);
            }
            return $this->returnErrorResponse('Error while uploading image');
        } catch (Exception $exception) {
            dd($exception);
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }
}
