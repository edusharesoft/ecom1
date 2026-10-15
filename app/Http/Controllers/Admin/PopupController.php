<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use App\Models\Popup;
use Illuminate\Support\Facades\File; 
use Toastr;

class PopupController extends Controller
{
    public function index()
    {
        $popups = Popup::latest()->get();
        return view('backEnd.popup.index', compact('popups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            $popup = new Popup();

            if ($request->hasFile('image')) {
                $popup->image = ImageOptimizer::store($request->file('image'), 'uploads/popup/');
            }

            $popup->title = $request->title ?: 'Promo Popup';
            $popup->description = $request->description;
            $popup->btn_text = $request->btn_text;
            $popup->offer_end_text = $request->offer_end_text;
            $popup->link = $request->link;
            $popup->status = $request->has('status') ? 1 : 0;
            $popup->save();

            if(function_exists('toastr')){
                \Toastr::success('Popup Created Successfully');
            }
            return redirect()->back()->with('success', 'Popup Created Successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $edit = Popup::find($id);
        return view('backEnd.popup.edit', compact('edit'));
    }

    public function update(Request $request)
    {
        $popup = Popup::find($request->hidden_id);

        if ($request->hasFile('image')) {
            if (File::exists(public_path($popup->image))) {
                File::delete(public_path($popup->image));
            }

            $popup->image = ImageOptimizer::store($request->file('image'), 'uploads/popup/');
        }

        $popup->title = $request->title ?: 'Promo Popup';
        $popup->description = $request->description;
            $popup->btn_text = $request->btn_text;
            $popup->offer_end_text = $request->offer_end_text;
            $popup->link = $request->link;
            $popup->status = $request->has('status') ? 1 : 0;
        $popup->save();

        if(function_exists('toastr')){
            \Toastr::success('Popup Updated Successfully');
        }
        return redirect()->route('admin.popup.index');
    }

    public function status($id)
    {
        $popup = Popup::find($id);
        $popup->status = $popup->status == 1 ? 0 : 1;
        $popup->save();
        
        if(function_exists('toastr')){
            \Toastr::success('Status Changed');
        }
        return redirect()->back();
    }

    public function destroy($id)
    {
        $popup = Popup::find($id);
        if (File::exists(public_path($popup->image))) {
            File::delete(public_path($popup->image));
        }
        $popup->delete();
        
        if(function_exists('toastr')){
            \Toastr::success('Popup Deleted');
        }
        return redirect()->back();
    }
}
