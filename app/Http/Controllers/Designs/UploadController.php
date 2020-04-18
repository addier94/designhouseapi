<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    
    public function upload(Request $request)
    {
        // validate the request
        $this->validate($request, [
            'image' => ['required', 'mimes:jpeg,gif,bmp,png', 'max:2048']
        ]);

        // get the image
        $image = $request->file('image');
        $image_path = $image->getPathName();

        // get the original file name and replace any spaces with _
        // business Cards.png = business_cards.png
        $filename = time()."_". preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));

        // move the image to the temporary location (tmp)
        $tmp = $image->storeAs('uploads/original', $filename, 'tmp');

        // create the database record for the design
        $design = auth()->user()->designs()->create([
            'image' => $filename,
            'disk' => config('site.upload_disk')
        ]);

        // dispatch a job to handle the image manipulation
        $this->dispatch(new UploadImage($design));

        return response()->json($design, 200);
    }
}
