<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;

class AuthController extends Controller
{
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        Toastr::success('You are logged out successfully', 'Success!');
        return redirect()->route('home');
    }
}
