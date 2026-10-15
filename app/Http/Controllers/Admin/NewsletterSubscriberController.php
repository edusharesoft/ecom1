<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class NewsletterSubscriberController extends Controller
{
    public function index(Request $request)
    {
        $subscribers = NewsletterSubscriber::latest()->paginate(20);

        if ($request->ajax()) {
            return view('backEnd.newsletterSubscriber.partials.table', compact('subscribers'))->render();
        }

        return view('backEnd.newsletterSubscriber.index', compact('subscribers'));
    }

    public function destroy(Request $request, $id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);
        $subscriber->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Subscriber deleted successfully',
            ]);
        }

        Toastr::success('Subscriber deleted successfully');
        return redirect()->back();
    }
}
