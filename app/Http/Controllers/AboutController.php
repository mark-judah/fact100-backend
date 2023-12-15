<?php

namespace App\Http\Controllers;

use App\Models\About;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function createAboutUs(Request $request)
    {
        $this->validate($request, [
            'content_body' => 'required|string',
        ]);
        About::create([
            'content_body' => $request->content_body
        ]);
        return response()->json([
            'table' => 'about',
            'action' => 'create',
            'result' => 'success'
        ], 201);
    }

    public function getAboutUs()
    {
        $about = About::orderBy('created_at', 'desc')->get();
        return json_encode($about);
    }

    public function editAboutUs(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|string',
            'content_body' => 'required|string',
        ]);
        $about = About::find($request->id);
        if ($about) {
            $about->where("id", $request->id)->update([
                'content_body' => $request->get('content_body'),
            ]);
        }

        return response()->json([
            'result' => 'About us edited successfully.'
        ], 201);
    }

    public function uploadAboutUsImage(Request $request)
    {
        \Tinify\setKey("1VzK915ZyBgpW4YSZwKtvccYLZ6F1sMp");

        $files = $request->file('about_us_image');
        // Define upload path
        $destinationPath = public_path('uploads/tinymce/about_images'); // upload path
        // Upload Orginal Image
        $aboutImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
        $files->move($destinationPath, $aboutImage);
        $source=\Tinify\fromFile("$destinationPath/".$aboutImage);
        $source->toFile("$destinationPath/".$aboutImage);
        $insert['image'] = "$aboutImage";
        return json_encode(['location' => "uploads/tinymce/about_images/$aboutImage"]);
    }

}
