<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TiktokPixel;
use Illuminate\Support\Facades\Cache;
use Toastr;

class TiktokPixelsController extends Controller
{
    public function index()
    {
        $data = TiktokPixel::orderBy('id', 'DESC')->get();
        return view('backEnd.tiktok_pixels.index', compact('data'));
    }

    public function create()
    {
        return view('backEnd.tiktok_pixels.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'code'   => 'required',
            'status' => 'required',
        ]);
        TiktokPixel::create($request->all());
        Cache::forget('tiktok_pixels_list');
        Toastr::success('TikTok Pixel added successfully!', 'Success');
        return redirect()->route('tiktok.pixels.index');
    }

    public function edit($id)
    {
        $edit_data = TiktokPixel::findOrFail($id);
        return view('backEnd.tiktok_pixels.edit', compact('edit_data'));
    }

    public function update(Request $request)
    {
        $this->validate($request, ['code' => 'required']);
        $pixel = TiktokPixel::find($request->hidden_id ?? $request->id);
        if (!$pixel) {
            Toastr::error('Record not found.', 'Error');
            return redirect()->back();
        }
        $input = $request->except('hidden_id');
        $input['status'] = $request->has('status') ? 1 : 0;
        $pixel->update($input);
        Cache::forget('tiktok_pixels_list');
        Toastr::success('TikTok Pixel updated successfully!', 'Success');
        return redirect()->route('tiktok.pixels.index');
    }

    public function inactive(Request $request)
    {
        $pixel = TiktokPixel::find($request->hidden_id);
        if (!$pixel) {
            Toastr::error('Record not found.', 'Error');
            return redirect()->back();
        }
        $pixel->status = 0;
        $pixel->save();
        Cache::forget('tiktok_pixels_list');
        Toastr::success('Pixel deactivated.', 'Success');
        return redirect()->back();
    }

    public function active(Request $request)
    {
        $pixel = TiktokPixel::find($request->hidden_id);
        if (!$pixel) {
            Toastr::error('Record not found.', 'Error');
            return redirect()->back();
        }
        $pixel->status = 1;
        $pixel->save();
        Cache::forget('tiktok_pixels_list');
        Toastr::success('Pixel activated.', 'Success');
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $pixel = TiktokPixel::find($request->hidden_id);
        if (!$pixel) {
            Toastr::error('Record not found.', 'Error');
            return redirect()->back();
        }
        $pixel->delete();
        Cache::forget('tiktok_pixels_list');
        Toastr::success('Pixel deleted.', 'Success');
        return redirect()->back();
    }
}
