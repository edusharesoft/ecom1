<?php

namespace App\Http\Controllers\Frontend;

use shurjopayv2\ShurjopayLaravelPackage8\Http\Controllers\ShurjopayController;
use App\Mail\OrderPlace;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Intervention\Image\Facades\Image;
use App\Models\Customer;
use App\Models\District;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Payment;
use App\Models\Shipping;
use App\Models\Review;
use App\Models\PaymentGateway;
use App\Models\ManualPaymentGateway;
use App\Models\DeliveryDivision;
use App\Support\DeliveryLocation;
use App\Models\SmsGateway;
use App\Helpers\SmsHelper;
use App\Models\Contact;
use App\Models\GeneralSetting;
use App\Models\IncompleteOrder;
use App\Models\Product;          // স্টক কমানোর জন্য
use App\Models\DigitalDownload;  // ⭐ ডিজিটাল ডাউনলোড মডেল

use Session;
use Hash;
use Auth;
use Cart;
use Mail;
use Str;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash as HashFacade;
use Illuminate\Support\Facades\File;
use App\Helpers\OrderHelper;
use App\Services\BdCourierService;
use App\Services\FacebookCapiService;
use App\Http\Controllers\Concerns\HandlesCheckoutOtp;

class CustomerController extends Controller
{
    use HandlesCheckoutOtp;

    protected $facebookCapiService;

    function __construct(FacebookCapiService $facebookCapiService)
    {
        $this->facebookCapiService = $facebookCapiService;
        $this->middleware('customer', ['except' => [
            'register',
            'store',
            'verify',
            'resendotp',
            'account_verify',
            'login',
            'signin',
            'logout',
            'checkout',
            'forgot_password',
            'forgot_verify',
            'forgot_reset',
            'forgot_store',
            'forgot_resend',
            'order_save',
            'order_success',
            'order_track',
            'order_track_result'
        ]]);
    }

    public function review(Request $request)
    {
        $this->validate($request, [
            'ratting' => 'required',
            'review' => 'required',
        ]);

        $review = new Review();
        $review->name = Auth::guard('customer')->user()->name ?? 'N / A';
        $review->email = Auth::guard('customer')->user()->email ?? 'N / A';
        $review->product_id = $request->product_id;
        $review->review = $request->review;
        $review->ratting = $request->ratting;
        $review->customer_id = Auth::guard('customer')->user()->id;
        $review->status = 'pending';
        $review->save();

        Toastr::success('Thanks, Your review send successfully', 'Success!');
        return redirect()->back();
    }

    public function login()
    {
        return view('frontEnd.layouts.customer.login');
    }

    public function signin(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $login    = $request->input('login');   // phone or email
        $password = $request->input('password');

        // Check if login is phone number and if it belongs to a vendor or reseller
        $isVendorPhone = false;
        $isResellerPhone = false;
        $vendor = null;
        $resellerUser = null;

        if (preg_match('/^[0-9+]+$/', $login)) {
            // Check if it's a vendor phone
            $vendor = \App\Models\Vendor::where('phone', $login)->first();
            if ($vendor) {
                $isVendorPhone = true;
            }

            // Check if it's a reseller phone (via customer record email matching)
            $customer = Customer::where('phone', $login)->first();
            if ($customer && $customer->email) {
                $resellerUser = \App\Models\User::where('email', $customer->email)
                    ->where(function ($query) {
                        $query->where('role', 'reseller')
                            ->orWhereHas('roles', function ($q) {
                                $q->where('name', 'reseller');
                            });
                    })
                    ->first();
                if ($resellerUser) {
                    $isResellerPhone = true;
                }
            }
        }

        // 1) Try customer (phone-based) - only if not a vendor or reseller phone
        // Also check if customer exists with this phone number
        if (!$isVendorPhone && !$isResellerPhone && preg_match('/^[0-9+]+$/', $login)) {
            $customerExists = Customer::where('phone', $login)->exists();
            if ($customerExists && Auth::guard('customer')->attempt(['phone' => $login, 'password' => $password])) {
                Toastr::success('You are login successfully', 'success!');
                if (Cart::instance('shopping')->count() > 0) {
                    return redirect()->route('customer.checkout');
                }
                return redirect()->intended('customer/account');
            }
        }

        // If reseller phone, use admin guard with reseller email
        if ($isResellerPhone && $resellerUser) {
            $adminCredentials = ['email' => $resellerUser->email, 'password' => $password];
        }

        // 2) Try vendor/admin/reseller via admin guard (email mapped from vendor/reseller phone or direct email)
        if (!isset($adminCredentials)) {
            $adminCredentials = null;
        }

        // If login looks like a phone number, map to vendor email (if not already set for reseller)
        if (!isset($adminCredentials) && preg_match('/^[0-9+]+$/', $login)) {
            // Re-check vendor if not already checked
            if (!$vendor) {
                $vendor = \App\Models\Vendor::where('phone', $login)->first();
            }
            if ($vendor) {
                $adminCredentials = ['email' => $vendor->email, 'password' => $password];
            }
        } elseif (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            // Email input - check if it's a reseller email first
            $resellerUser = \App\Models\User::where('email', $login)
                ->where(function ($query) {
                    $query->where('role', 'reseller')
                        ->orWhereHas('roles', function ($q) {
                            $q->where('name', 'reseller');
                        });
                })
                ->first();

            if ($resellerUser) {
                // It's a reseller email, use admin guard
                $adminCredentials = ['email' => $login, 'password' => $password];
            } else {
                // Check if it's a vendor email
                $vendor = \App\Models\Vendor::where('email', $login)->first();
                if ($vendor) {
                    // It's a vendor email, use admin guard
                    $adminCredentials = ['email' => $login, 'password' => $password];
                } else {
                    // Check if it's a customer email (but not a reseller)
                    $customerExists = Customer::where('email', $login)->exists();
                    // Also check if this customer email is not linked to a reseller
                    $isResellerCustomer = \App\Models\User::where('email', $login)
                        ->where(function ($query) {
                            $query->where('role', 'reseller')
                                ->orWhereHas('roles', function ($q) {
                                    $q->where('name', 'reseller');
                                });
                        })
                        ->exists();

                    if ($customerExists && !$isResellerCustomer && Auth::guard('customer')->attempt(['email' => $login, 'password' => $password])) {
                        Toastr::success('You are login successfully', 'success!');
                        if (Cart::instance('shopping')->count() > 0) {
                            return redirect()->route('customer.checkout');
                        }
                        return redirect()->intended('customer/account');
                    }
                    // Try admin/user email
                    $adminCredentials = ['email' => $login, 'password' => $password];
                }
            }
        }

        if ($adminCredentials && Auth::guard('admin')->attempt($adminCredentials)) {
            $user = Auth::guard('admin')->user();

            // Check if user has reseller role, redirect to reseller dashboard
            // Check both Spatie role and direct role column
            $isReseller = $user->hasRole('reseller') ||
                (isset($user->role) && strtolower($user->role) === 'reseller') ||
                $user->getRoleNames()->contains('reseller');

            if ($isReseller) {
                Toastr::success('You are login successfully', 'success!');
                return redirect()->route('reseller.dashboard');
            }

            if ($user->hasRole('vendor')) {
                Toastr::success('You are login successfully', 'success!');
                return redirect()->route('vendor.dashboard');
            }
            if ($user->hasRole('admin')) {
                Toastr::success('You are login successfully', 'success!');
                return redirect()->route('admin.dashboard');
            }

            // Unknown role -> logout and show error
            Auth::guard('admin')->logout();
            Toastr::error('Role not allowed for this login path', 'Error');
            return redirect()->back();
        }

        // Failed
        Toastr::error('Opps! your credentials are wrong', 'Error');
        return redirect()->back()->withInput($request->only('login'));
    }

    public function register()
    {
        return view('frontEnd.layouts.customer.register');
    }

    public function store(Request $request)
    {
        $isReseller = $request->has('is_reseller') && $request->is_reseller == '1';
        $isSeller = $request->has('is_seller') && $request->is_seller == '1';

        if ($isReseller) {
            // Reseller registration — ডকিমেন্ট ড্যাশবোর্ডের ভেরিফিকেশন পেজে (Reseller\\VerificationController)
            $request->validate([
                'name'               => 'required|string|max:255',
                'phone'              => 'required|string|max:55|unique:customers,phone',
                'email'              => 'required|email|unique:users,email|unique:customers,email',
                'reseller_shop_name' => 'required|string|max:255',
                'password'           => 'required|confirmed|min:6',
                'voter_id_front'     => 'nullable|image|mimes:jpeg,jpg,png,webp|max:102400',
                'voter_id_back'      => 'nullable|image|mimes:jpeg,jpg,png,webp|max:102400',
                'self_image'         => 'nullable|image|mimes:jpeg,jpg,png,webp|max:102400',
            ]);

            // Upload verification documents
            $voterFrontPath = null;
            $voterBackPath = null;
            $selfImagePath = null;

            if ($request->hasFile('voter_id_front')) {
                $frontImage = $request->file('voter_id_front');
                $frontName = time() . '-voter-front-' . uniqid() . '.webp';
                $frontPath = 'public/uploads/reseller/verification/';

                if (!File::exists($frontPath)) {
                    File::makeDirectory($frontPath, 0755, true);
                }

                $img = Image::make($frontImage->getRealPath());
                $img->encode('webp', 90);
                $img->resize(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $img->save($frontPath . $frontName);
                $voterFrontPath = $frontPath . $frontName;
            }

            if ($request->hasFile('voter_id_back')) {
                $backImage = $request->file('voter_id_back');
                $backName = time() . '-voter-back-' . uniqid() . '.webp';
                $backPath = 'public/uploads/reseller/verification/';

                if (!File::exists($backPath)) {
                    File::makeDirectory($backPath, 0755, true);
                }

                $img = Image::make($backImage->getRealPath());
                $img->encode('webp', 90);
                $img->resize(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $img->save($backPath . $backName);
                $voterBackPath = $backPath . $backName;
            }

            if ($request->hasFile('self_image')) {
                $selfImage = $request->file('self_image');
                $selfName = time() . '-self-' . uniqid() . '.webp';
                $selfPath = 'public/uploads/reseller/verification/';

                if (!File::exists($selfPath)) {
                    File::makeDirectory($selfPath, 0755, true);
                }

                $img = Image::make($selfImage->getRealPath());
                $img->encode('webp', 90);
                $img->resize(600, 600, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $img->save($selfPath . $selfName);
                $selfImagePath = $selfPath . $selfName;
            }

            // Create user account with reseller role
            $user = \App\Models\User::create([
                'name'                  => $request->name,
                'email'                 => $request->email,
                'password'              => HashFacade::make($request->password),
                'status'                => 1,
                'role'                  => 'reseller',
                'shop_name'             => $request->reseller_shop_name,
                'verification_status'   => 'pending',
                'voter_id_front'        => $voterFrontPath,
                'voter_id_back'         => $voterBackPath,
                'self_image'            => $selfImagePath,
            ]);

            // Ensure reseller role exists and assign (using admin guard like vendors)
            $role = \Spatie\Permission\Models\Role::firstOrCreate(
                ['name' => 'reseller', 'guard_name' => 'admin'],
                ['name' => 'reseller', 'guard_name' => 'admin']
            );
            $user->assignRole($role);

            // Clear role cache to ensure role is immediately available
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            // Also create customer record for phone-based login
            $last_id = Customer::orderBy('id', 'desc')->first();
            $last_id = $last_id ? $last_id->id + 1 : 1;

            $customer = new Customer();
            $customer->name = $request->name;
            $customer->slug = strtolower(Str::slug($request->name . '-' . $last_id));
            $customer->phone = $request->phone;
            $customer->email = $request->email;
            $customer->password = bcrypt($request->password);
            $customer->verify = 1;
            $customer->status = 'active';
            $customer->save();

            // Assign customer role to customer record
            $customerRole = \Spatie\Permission\Models\Role::firstOrCreate(
                ['name' => 'customer', 'guard_name' => 'customer'],
                ['name' => 'customer', 'guard_name' => 'customer']
            );
            $customer->assignRole($customerRole);

            // Auto login reseller after registration
            Auth::guard('admin')->login($user);

            Toastr::success('রিসেলার একাউন্ট তৈরি হয়েছে। ড্যাশবোর্ডের ভেরিফিকেশন পেজ থেকে এনআইডি ও ছবি আপলোড করে সম্পন্ন করুন।', 'Success');
            return redirect()->route('reseller.dashboard');
        } elseif ($isSeller) {
            // Vendor registration
            $request->validate([
                'name'                  => 'required|string|max:255',
                'phone'                 => 'required|string|max:55|unique:vendors,phone|unique:customers,phone',
                'email'                 => 'required|email|unique:users,email|unique:vendors,email|unique:customers,email',
                'shop_name'             => 'required|string|max:255',
                'slug'                  => 'required|string|max:255|unique:vendors,slug',
                'password'              => 'required|confirmed|min:6',
                'address'               => 'nullable|string',
                'logo'                  => 'nullable|image|max:2048',
                'banner'                => 'nullable|image|max:3072',
            ]);

            // Upload files if provided
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('uploads/vendor/logo', 'public');
            }

            $bannerPath = null;
            if ($request->hasFile('banner')) {
                $bannerPath = $request->file('banner')->store('uploads/vendor/banner', 'public');
            }

            // Create vendor record first
            $vendor = \App\Models\Vendor::create([
                'shop_name'  => $request->shop_name,
                'slug'       => $request->slug,
                'owner_name' => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'address'    => $request->address ?? null,
                'logo'       => $logoPath,
                'banner'     => $bannerPath,
                'status'     => 1,
            ]);

            // Create user account with vendor_id
            $user = \App\Models\User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => HashFacade::make($request->password),
                'status'    => 1,
                'vendor_id' => $vendor->id,
            ]);

            // Ensure vendor role exists and assign
            $role = \Spatie\Permission\Models\Role::firstOrCreate(
                ['name' => 'vendor', 'guard_name' => 'admin'],
                ['name' => 'vendor', 'guard_name' => 'admin']
            );
            $user->assignRole($role);

            // Auto login vendor after registration
            Auth::guard('admin')->login($user);

            Toastr::success('ভেন্ডর একাউন্ট তৈরি হয়েছে। ড্যাশবোর্ডের ভেরিফিকেশন অংশ থেকে এনআইডি ও ছবি আপলোড করে ভেরিফিকেশন সম্পূর্ণ করুন।', 'Success');
            return redirect()->route('vendor.dashboard');
        } else {
            // Customer registration
            $this->validate($request, [
                'name'     => 'required',
                'phone'    => 'required|unique:customers',
                'password' => 'required|min:6'
            ]);

            $last_id = Customer::orderBy('id', 'desc')->first();
            $last_id = $last_id ? $last_id->id + 1 : 1;

            $store = new Customer();
            $store->name = $request->name;
            $store->slug = strtolower(Str::slug($request->name . '-' . $last_id));
            $store->phone = $request->phone;
            $store->email = $request->email ?? null;
            $store->password = bcrypt($request->password);
            $store->verify = 1;
            $store->status = 'active';
            $store->save();

            // Assign customer role
            $customerRole = \Spatie\Permission\Models\Role::firstOrCreate(
                ['name' => 'customer', 'guard_name' => 'customer'],
                ['name' => 'customer', 'guard_name' => 'customer']
            );
            $store->assignRole($customerRole);

            Toastr::success('Success', 'Account Create Successfully');
            return redirect()->route('customer.login');
        }
    }

    public function verify()
    {
        return view('frontEnd.layouts.customer.verify');
    }

    public function resendotp(Request $request)
    {
        $customer_info = Customer::where('phone', session::get('verify_phone'))->first();
        $customer_info->verify = rand(1111, 9999);
        $customer_info->save();
        $site_setting = GeneralSetting::where('status', 1)->first();
        SmsHelper::send(
            $customer_info->phone,
            "Dear {$customer_info->name}!\r\nYour account verify OTP is {$customer_info->verify}\r\nThank you for using {$site_setting->name}"
        );

        Toastr::success('Success', 'Resend code send successfully');
        return redirect()->back();
    }

    public function account_verify(Request $request)
    {
        $this->validate($request, ['otp' => 'required']);
        $customer_info = Customer::where('phone', session::get('verify_phone'))->first();

        if ($customer_info->verify != $request->otp) {
            Toastr::error('Success', 'Your OTP not match');
            return redirect()->back();
        }

        $customer_info->verify = 1;
        $customer_info->status = 'active';
        $customer_info->save();
        Auth::guard('customer')->loginUsingId($customer_info->id);
        return redirect()->route('customer.account');
    }

    public function forgot_password()
    {
        return view('frontEnd.layouts.customer.forgot_password');
    }

    public function forgot_verify(Request $request)
    {
        $phone = $request->phone;
        $customer_info = null;
        $vendor_info = null;
        $reseller_user = null;
        $user_type = null;
        $name = null;

        // First check if phone belongs to a reseller (priority check)
        $customer = Customer::where('phone', $phone)->first();
        if ($customer && $customer->email) {
            $reseller_user = \App\Models\User::where('email', $customer->email)
                ->where(function ($query) {
                    $query->where('role', 'reseller')
                        ->orWhereHas('roles', function ($q) {
                            $q->where('name', 'reseller');
                        });
                })
                ->first();
            if ($reseller_user) {
                $user_type = 'reseller';
                $name = $reseller_user->name ?? $customer->name;
                // Store OTP in session for reseller
                Session::put('reseller_forgot_otp', rand(1111, 9999));
            } else {
                // Regular customer
                $customer_info = $customer;
                $user_type = 'customer';
                $name = $customer_info->name;
                $customer_info->forgot = rand(1111, 9999);
                $customer_info->save();
            }
        } else {
            // Check Vendor
            $vendor_info = \App\Models\Vendor::where('phone', $phone)->first();
            if ($vendor_info) {
                $user_type = 'vendor';
                $name = $vendor_info->owner_name;
                $vendor_info->forgot = rand(1111, 9999);
                $vendor_info->save();
            }
        }

        if (!$customer_info && !$vendor_info && !$reseller_user) {
            Toastr::error('Your phone number not found');
            return back();
        }

        $site_setting = GeneralSetting::where('status', 1)->first();
        $otp = $customer_info ? $customer_info->forgot : ($vendor_info ? $vendor_info->forgot : Session::get('reseller_forgot_otp'));

        SmsHelper::send(
            $phone,
            "Dear {$name}!\r\nYour forgot password verify OTP is {$otp}\r\nThank you for using {$site_setting->name}",
            ['forget_pass' => 1]
        );

        Session::put('verify_phone', $phone);
        Session::put('user_type', $user_type);
        Toastr::success('OTP sent successfully to your phone');
        return redirect()->route('customer.forgot.reset');
    }

    public function forgot_resend(Request $request)
    {
        $phone = Session::get('verify_phone');
        $user_type = Session::get('user_type');
        $customer_info = null;
        $vendor_info = null;
        $reseller_user = null;
        $name = null;
        $otp = null;

        if ($user_type == 'customer') {
            $customer_info = Customer::where('phone', $phone)->first();
            if ($customer_info) {
                $customer_info->forgot = rand(1111, 9999);
                $customer_info->save();
                $name = $customer_info->name;
                $otp = $customer_info->forgot;
            }
        } elseif ($user_type == 'vendor') {
            $vendor_info = \App\Models\Vendor::where('phone', $phone)->first();
            if ($vendor_info) {
                $vendor_info->forgot = rand(1111, 9999);
                $vendor_info->save();
                $name = $vendor_info->owner_name;
                $otp = $vendor_info->forgot;
            }
        } elseif ($user_type == 'reseller') {
            $customer = Customer::where('phone', $phone)->first();
            if ($customer && $customer->email) {
                $reseller_user = \App\Models\User::where('email', $customer->email)
                    ->where(function ($query) {
                        $query->where('role', 'reseller')
                            ->orWhereHas('roles', function ($q) {
                                $q->where('name', 'reseller');
                            });
                    })
                    ->first();
                if ($reseller_user) {
                    $otp = rand(1111, 9999);
                    Session::put('reseller_forgot_otp', $otp);
                    $name = $reseller_user->name ?? $customer->name;
                }
            }
        }

        if (!$customer_info && !$vendor_info && !$reseller_user) {
            Toastr::error('Something went wrong');
            return redirect()->route('customer.forgot.password');
        }

        $site_setting = GeneralSetting::where('status', 1)->first();
        SmsHelper::send(
            $phone,
            "Dear {$name}!\r\nYour forgot password verify OTP is {$otp}\r\nThank you for using {$site_setting->name}"
        );

        Toastr::success('Success', 'Resend code send successfully');
        return redirect()->back();
    }

    public function forgot_reset()
    {
        if (!Session::get('verify_phone')) {
            Toastr::error('Something wrong please try again');
            return redirect()->route('customer.forgot.password');
        }
        return view('frontEnd.layouts.customer.forgot_reset');
    }

    public function forgot_store(Request $request)
    {
        $phone = Session::get('verify_phone');
        $user_type = Session::get('user_type');
        $customer_info = null;
        $vendor_info = null;
        $reseller_user = null;

        if ($user_type == 'customer') {
            $customer_info = Customer::where('phone', $phone)->first();
            if (!$customer_info || $customer_info->forgot != $request->otp) {
                Toastr::error('Your OTP not match');
                return redirect()->back();
            }
            $customer_info->forgot = 1;
            $customer_info->password = bcrypt($request->password);
            $customer_info->save();
            if (Auth::guard('customer')->attempt(['phone' => $customer_info->phone, 'password' => $request->password])) {
                Session::forget('verify_phone');
                Session::forget('user_type');
                Toastr::success('Password reset successfully. You are logged in!', 'Success!');
                return redirect()->intended('customer/account');
            }
        } elseif ($user_type == 'vendor') {
            $vendor_info = \App\Models\Vendor::where('phone', $phone)->first();
            if (!$vendor_info || $vendor_info->forgot != $request->otp) {
                Toastr::error('Your OTP not match');
                return redirect()->back();
            }
            // Find user by vendor email (vendor login uses admin guard with User model)
            $user = \App\Models\User::where('email', $vendor_info->email)->first();
            if (!$user) {
                Toastr::error('User account not found');
                return redirect()->route('customer.forgot.password');
            }
            $user->password = bcrypt($request->password);
            $user->save();
            $vendor_info->forgot = 1;
            $vendor_info->save();
            Session::forget('verify_phone');
            Session::forget('user_type');
            Toastr::success('Password reset successfully. Please login with your new password.', 'Success!');
            return redirect()->route('customer.login');
        } elseif ($user_type == 'reseller') {
            $stored_otp = Session::get('reseller_forgot_otp');
            if ($stored_otp != $request->otp) {
                Toastr::error('Your OTP not match');
                return redirect()->back();
            }
            $customer = Customer::where('phone', $phone)->first();
            if ($customer && $customer->email) {
                $reseller_user = \App\Models\User::where('email', $customer->email)
                    ->where(function ($query) {
                        $query->where('role', 'reseller')
                            ->orWhereHas('roles', function ($q) {
                                $q->where('name', 'reseller');
                            });
                    })
                    ->first();
                if ($reseller_user) {
                    // Update password in User table (for reseller panel login)
                    $reseller_user->password = bcrypt($request->password);
                    $reseller_user->save();

                    // Also update password in Customer table (for customer dashboard login)
                    $customer->password = bcrypt($request->password);
                    $customer->save();

                    Session::forget('verify_phone');
                    Session::forget('user_type');
                    Session::forget('reseller_forgot_otp');
                    Toastr::success('Password reset successfully. Please login with your new password.', 'Success!');
                    return redirect()->route('customer.login');
                }
            }
        }

        Toastr::error('Something went wrong');
        return redirect()->route('customer.forgot.password');
    }

    public function account()
    {
        return view('frontEnd.layouts.customer.account');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        Toastr::success('You are logout successfully', 'success!');
        return redirect()->route('customer.login');
    }

    public function checkout()
    {
        $this->checkoutOtpSweepExpired('customer');

        $divisions = DeliveryDivision::active()->ordered()->get();
        $bkash_gateway = PaymentGateway::where(['status' => 1, 'type' => 'bkash'])->first();
        $shurjopay_gateway = PaymentGateway::where(['status' => 1, 'type' => 'shurjopay'])->first();
        $uddoktapay_gateway = PaymentGateway::where(['status' => 1, 'type' => 'uddoktapay'])->first();
        $aamarpay_gateway = PaymentGateway::where(['status' => 1, 'type' => 'aamarpay'])->first();
        $shatajpay = PaymentGateway::where('type', 'shatajpay')->first();
        $manual_gateways = ManualPaymentGateway::enabled()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $hasAllFreeDelivery = \App\Http\Controllers\Frontend\ShoppingController::hasAllFreeDeliveryProducts();

        $requiresPhysicalShipping = false;
        foreach (Cart::instance('shopping')->content() as $item) {
            $product = Product::find($item->id);
            if ($product && (int) $product->is_digital !== 1) {
                $requiresPhysicalShipping = true;
                break;
            }
        }

        if (! $requiresPhysicalShipping || $hasAllFreeDelivery) {
            Session::put('shipping', 0);
            Session::put('shipping_district_id', null);
        }

        $advanceTotal = \App\Http\Controllers\Frontend\ShoppingController::getCartAdvanceAmount();
        $hasAdvance   = $advanceTotal > 0;

        $hasDigital = \App\Http\Controllers\Frontend\ShoppingController::hasDigitalProductInCart();

        if (Auth::guard('admin')->check()) {
            $resellerUser = Auth::guard('admin')->user();
            $isReseller = $resellerUser->hasRole('reseller') ||
                (isset($resellerUser->role) && strtolower($resellerUser->role) === 'reseller') ||
                $resellerUser->getRoleNames()->contains('reseller');

            if ($isReseller && Cart::instance('shopping')->count() > 0) {
                return redirect()->route('reseller.checkout');
            }
        }

        \App\Http\Controllers\Frontend\ShoppingController::refreshCartWholesalePrices();

        return view('frontEnd.layouts.customer.checkout', compact(
            'divisions',
            'bkash_gateway',
            'shurjopay_gateway',
            'uddoktapay_gateway',
            'aamarpay_gateway',
            'shatajpay',
            'manual_gateways',
            'advanceTotal',
            'hasAdvance',
            'hasDigital',
            'hasAllFreeDelivery'
        ));
    }

    public function order_save(Request $request)
    {
        if ($request->boolean('checkout_otp_resend')) {
            return $this->checkoutOtpResendForChannel($request, 'customer', false);
        }

        if (Session::get('chkotp_customer_pending')) {
            $this->checkoutOtpApplyDraft($request, 'customer', true);
        }

        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ]);

        if (Cart::instance('shopping')->count() <= 0) {
            Toastr::error('Your shopping empty', 'Failed!');
            return redirect()->back();
        }

        \App\Http\Controllers\Frontend\ShoppingController::refreshCartWholesalePrices();

        // ⭐ কার্টে ডিজিটাল প্রোডাক্ট আছে কি না চেক
        $hasDigital = \App\Http\Controllers\Frontend\ShoppingController::hasDigitalProductInCart();

        if ($hasDigital && $request->payment_method === 'cod') {
            Toastr::error('ডিজিটাল প্রোডাক্টের জন্য Cash On Delivery পাওয়া যায় না, অনুগ্রহ করে অনলাইন পেমেন্ট সিলেক্ট করুন।', 'Failed!');
            return redirect()->back();
        }

        $manualPayId = ManualPaymentGateway::manualIdFromPaymentMethod($request->payment_method);
        if ($manualPayId !== null) {
            $mg = ManualPaymentGateway::where('id', $manualPayId)->where('status', 1)->first();
            if (! $mg) {
                Toastr::error('ম্যানুয়াল পেমেন্ট অপশনটি আর উপলব্ধ নেই। আবার সিলেক্ট করুন।', 'Failed!');
                return redirect()->back();
            }
            $this->validate($request, [
                'manual_trx_id'        => ['required', 'string', 'max:55'],
                'manual_sender_number' => ['nullable', 'string', 'max:55'],
            ]);
        }

        $requiresPhysicalShipping = false;
        foreach (Cart::instance('shopping')->content() as $item) {
            $product = Product::find($item->id);
            if ($product && (int) $product->is_digital !== 1) {
                $requiresPhysicalShipping = true;
                break;
            }
        }
        $hasAllFreeDelivery = \App\Http\Controllers\Frontend\ShoppingController::hasAllFreeDeliveryProducts();

        $divisionId = null;
        $districtId = null;
        $upazilaId = null;

        $checkoutOtpVerified = false;

        // OTP মুলতুবি থাকলে আগে যাচাই — সেশন শুধু সব ভ্যালিডেশন পাসের পর মুছব
        if ($this->checkoutOtpIsEnabled() && Session::get('chkotp_customer_pending')) {
            $otpRedirect = $this->checkoutOtpGate($request, 'customer', false);
            if ($otpRedirect !== null) {
                return $otpRedirect;
            }
            $checkoutOtpVerified = true;
        }

        if ($requiresPhysicalShipping && ! $hasAllFreeDelivery) {
            $this->validate($request, [
                'division_id' => 'required|exists:divisions,id',
                'district_id' => 'required|exists:districts,id',
                'upazila_id'  => 'required|exists:upazilas,id',
            ]);
            $divisionId = (int) $request->division_id;
            $districtId = (int) $request->district_id;
            $upazilaId = (int) $request->upazila_id;
            if (! DeliveryLocation::validateChain($divisionId, $districtId, $upazilaId)) {
                Toastr::error('বিভাগ, জেলা ও উপজেলা সঠিকভাবে নির্বাচন করুন।', 'Failed!');
                return redirect()->back()->withInput();
            }
        }

        if ($this->checkoutOtpIsEnabled() && ! $checkoutOtpVerified) {
            $otpRedirect = $this->checkoutOtpGate($request, 'customer');
            if ($otpRedirect !== null) {
                return $otpRedirect;
            }
        }

        if ($checkoutOtpVerified) {
            $this->checkoutOtpForget('customer');
        }

        // Amount ক্যালকুলেশন
        $subtotal = (float) str_replace([',', '.00'], '', Cart::instance('shopping')->subtotal());
        $discount = Session::get('discount', 0);

        if ($requiresPhysicalShipping && ! $hasAllFreeDelivery) {
            $shippingfee = DeliveryLocation::chargeForDistrictId($districtId);
            Session::put('shipping', $shippingfee);
            Session::put('shipping_district_id', $districtId);
        } else {
            $shippingfee = 0;
            Session::put('shipping', 0);
            Session::put('shipping_district_id', null);
        }

        $locationLabelForGateway = ($divisionId && $districtId && $upazilaId)
            ? DeliveryLocation::shippingLabel($divisionId, $districtId, $upazilaId)
            : 'BD';

        // কার্টের advance item গুলোর মোট
        $advanceTotal = \App\Http\Controllers\Frontend\ShoppingController::getCartAdvanceAmount();

        // ইনভয়েসে দেখানোর মোট (Grand Total)
        $grandTotal = ($subtotal + $shippingfee) - $discount;

        // =========================================================
        // ⭐ ফিক্সড লজিক: গেটওয়েতে কত টাকা পাঠাবো?
        // =========================================================
        // যদি এডভান্স থাকে, তাহলে শুধু এডভান্স এমাউন্ট পে করতে হবে।
        // যদি না থাকে, তাহলে পুরো গ্র্যান্ড টোটাল পে করতে হবে।
        $payable_amount = ($advanceTotal > 0) ? $advanceTotal : $grandTotal;

        // Customer ঠিক করা
        if (Auth::guard('customer')->user()) {
            $customer_id = Auth::guard('customer')->user()->id;
        } else {
            $exist = Customer::where('phone', $request->phone)->select('id')->first();
            if ($exist) {
                $customer_id = $exist->id;
            } else {
                $password = rand(111111, 999999);
                $store = new Customer();
                $store->name = $request->name;
                $store->slug = Str::slug($request->name);
                $store->phone = $request->phone;
                $store->password = bcrypt($password);
                $store->verify = 1;
                $store->status = 'active';
                $store->save();
                $customer_id = $store->id;
            }
        }

        // Main Order save
        $order = new Order();
        $order->invoice_id      = rand(11111, 99999);
        $order->amount          = $grandTotal; // অর্ডারে সবসময় টোটাল এমাউন্ট থাকবে
        $order->shipping_charge = $shippingfee;
        $order->customer_id     = $customer_id;
        $order->order_status    = 1;
        $order->note            = $request->note;
        $order->order_note      = $request->order_note;
        $order->payment_status  = 'pending';
        $order->coupon_code     = Session::get('coupon_code') ?? null;
        $order->discount        = $discount ?? 0;
        $order->ip_address      = $request->ip();
        // Traffic source — ফর্ম + সেশন (শেয়ার লিঙ্ক / referrer / fbclid)
        $rawSource = $request->input('traffic_source', session('order_traffic_source', 'direct'));
        $rawReferrer = $request->input('traffic_referrer', session('order_traffic_referrer', ''));
        $order->traffic_source = \App\Support\TrafficSourceDetector::normalize($rawSource);
        $order->traffic_referrer = \App\Support\TrafficSourceDetector::clip((string) $rawReferrer);

        $order->save();

        // Shipping info
        $shipping = new Shipping();
        $shipping->order_id    = $order->id;
        $shipping->customer_id = $customer_id;
        $shipping->name        = $request->name;
        $shipping->phone       = $request->phone;
        $shipping->address     = $request->address;
        $shipping->division_id = $divisionId;
        $shipping->district_id = $districtId;
        $shipping->upazila_id  = $upazilaId;
        $shipping->area        = ($divisionId && $districtId && $upazilaId)
            ? DeliveryLocation::shippingLabel($divisionId, $districtId, $upazilaId)
            : 'Digital / Free Shipping';
        $shipping->save();

        // BD Courier — ফোন অনুযায়ী সফলতার হার অর্ডার লিস্টে দেখাতে (চেকআউট রেসপন্স ব্লক না করে রিকোয়েস্ট শেষ হওয়ার পর রান)
        $fraudSyncPhone = trim((string) ($shipping->phone ?? ''));
        if ($fraudSyncPhone !== '' && config('services.bdcourier.api_key')) {
            dispatch(static function () use ($fraudSyncPhone): void {
                BdCourierService::fetchAndSyncOrders($fraudSyncPhone);
            })->afterResponse();
        }

        // Payment info
        $payment = new Payment();
        $payment->order_id       = $order->id;
        $payment->customer_id    = $customer_id;
        $payment->payment_method = $request->payment_method;

        // =========================================================
        // ⭐ ফিক্সড লজিক: ডাটাবেসে কত টাকা সেভ করব?
        // =========================================================
        if (ManualPaymentGateway::usesHostedRedirect($request->payment_method)) {
            // অনলাইন পেমেন্ট: শুরুতে ০ — কলব্যাকে আপডেট হবে
            $payment->amount = 0;
        } elseif ($manualPayId !== null) {
            $payment->amount                    = 0;
            $payment->manual_payable_snapshot   = max(0, (int) round($payable_amount));
            $payment->trx_id                   = $request->manual_trx_id;
            $payment->sender_number            = $request->filled('manual_sender_number') ? $request->manual_sender_number : null;
        } else {
            // COD
            $payment->amount = $payable_amount;
        }

        $payment->payment_status = 'pending';
        $payment->save();

        // Order details save
        OrderHelper::saveOrderDetails($order);

        // Stock reduce
        $details = OrderDetails::where('order_id', $order->id)
            ->with('product:id,stock')
            ->get();

        foreach ($details as $row) {
            if ($row->product) {
                $row->product->stock = max(0, $row->product->stock - $row->qty);
                $row->product->save();
            }
        }

        // === Customer SMS ===
        try {
            $customerPhone = isset($shipping) && $shipping->phone ? $shipping->phone : ($request->phone ?? ($order->customer->phone ?? null));
            $customerName  = isset($shipping) && $shipping->name ? $shipping->name : ($request->name ?? ($order->customer->name ?? 'Customer'));
            $site_setting  = GeneralSetting::where('status', 1)->first();
            if ($customerPhone) {
                SmsHelper::send($customerPhone, "প্রিয় {$customerName}! আপনার অর্ডার #{$order->invoice_id} সফলভাবে গ্রহণ করা হয়েছে। মোট: {$order->amount} Tk. {$site_setting->name}", ['order' => 1]);
            }
        } catch (\Exception $e) {
            \Log::error("Customer SMS error for order {$order->id}: " . $e->getMessage());
        }

        // === Admin SMS ===
        try {
            $site_setting  = isset($site_setting) ? $site_setting : GeneralSetting::where('status', 1)->first();
            $customerName  = $request->name ?? ($order->customer->name ?? 'Customer');
            $customerPhone = $request->phone ?? ($order->customer->phone ?? '');
            SmsHelper::sendToAdmin("নতুন অর্ডার!\nOrder#: {$order->invoice_id}\nকাস্টমার: {$customerName}\nমোবাইল: {$customerPhone}\nমোট: {$order->amount} Tk — {$site_setting->name}");
        } catch (\Exception $e) {
            \Log::error('Admin SMS send failed: ' . $e->getMessage());
        }

        // === Admin Order Email Notification (non-blocking — response পাঠানোর পর send হবে) ===
        try {
            $adminEmail = null;

            $site_setting = isset($site_setting) ? $site_setting : GeneralSetting::where('status', 1)->first();
            if (!empty($site_setting->email)) {
                $adminEmail = $site_setting->email;
            }
            if (empty($adminEmail)) {
                $contact = isset($contact) ? $contact : Contact::first();
                $adminEmail = $contact->email ?? null;
            }
            if (empty($adminEmail)) {
                $adminEmail = env('MAIL_FROM_ADDRESS');
            }

            if ($adminEmail) {
                $orderId    = $order->id;
                $invoiceId  = $order->invoice_id;
                $emailTo    = $adminEmail;

                // response পাঠানোর পর run হবে — user কোনো delay দেখবে না
                app()->terminating(function () use ($orderId, $invoiceId, $emailTo) {
                    try {
                        $freshOrder = \App\Models\Order::find($orderId);
                        if ($freshOrder) {
                            \Mail::to($emailTo)->send(new \App\Mail\OrderPlace($freshOrder));
                            \Log::info("Admin order email sent to {$emailTo} for order #{$invoiceId}");
                        }
                    } catch (\Exception $e) {
                        \Log::error("Admin order email failed for order #{$invoiceId}: " . $e->getMessage());
                    }
                });
            }
        } catch (\Exception $e) {
            \Log::error('Admin order email setup failed: ' . $e->getMessage());
        }

        // Incomplete order delete
        IncompleteOrder::where('phone', $request->phone)->delete();

        // =========================================================
        // ⭐ পেমেন্ট গেটওয়ে রিডাইরেক্ট (FIXED)
        // =========================================================

        // Bkash এবং UddoktaPay এর জন্য সেশনে এমাউন্ট সেট করে দিচ্ছি 
        // যাতে ওই কন্ট্রোলারগুলো সঠিক এমাউন্ট পায়
        Session::put('payable_amount', $payable_amount);

        if ($request->payment_method == 'bkash') {
            Session::forget('coupon_code');
            Session::forget('discount');
            return redirect('/bkash/checkout-url/create?order_id=' . $order->id);
        } elseif ($request->payment_method == 'shurjopay') {

            $info = [
                'currency'        => "BDT",
                'amount'          => $payable_amount, // ✅ এখানে ফিক্স করা হলো: এডভান্স থাকলে এডভান্স, না হলে ফুল
                'order_id'        => uniqid(),
                'client_ip'       => $request->ip(),
                'customer_name'   => $request->name,
                'customer_phone'  => $request->phone,
                'email'           => "customer@gmail.com",
                'customer_address' => $request->address,
                'customer_city'   => $locationLabelForGateway,
                'customer_country' => "BD",
                'value1'          => $order->id
            ];

            Session::forget('coupon_code');
            Session::forget('discount');

            $sp = new ShurjopayController();
            return $sp->checkout($info);
        } elseif ($request->payment_method == 'uddoktapay') {
            Session::forget('coupon_code');
            Session::forget('discount');
            return redirect()->route('uddoktapay.checkout', ['order_id' => $order->id]);
        } elseif ($request->payment_method == 'aamarpay') {
            Session::forget('coupon_code');
            Session::forget('discount');
            return redirect()->route('aamarpay.checkout', ['order_id' => $order->id]);
        } elseif ($request->payment_method == 'shatajpay') {
            Session::forget('coupon_code');
            Session::forget('discount');

            $paymentInfo = [
                'currency'          => 'BDT',
                'amount'            => $payable_amount,
                'trans_id'          => uniqid(),
                'order_id'          => uniqid(),
                'customer_name'     => $request->name,
                'customer_phone'    => $request->phone,
                'email'             => $request->email ?? 'customer@gmail.com',
                'customer_address' => $request->address,
            ];

            $shatajPayController = new ShatajPayController();


            return $shatajPayController->ShatajPayInitPayment([
                'order_id'    => $order->id,
                'paymentInfo' => $paymentInfo,
            ]);
        } elseif (
            $request->payment_method === 'cod'
            || ManualPaymentGateway::isManualPaymentMethod($request->payment_method)
        ) {
            // Cash On Delivery বা ম্যানুয়াল গাইডেড পেমেন্ট — সরাসরি সাফল্য পেইজ
            $this->createDigitalDownloads($order);

            // Send Facebook Purchase event for COD orders (async - don't block order submission)
            try {
                $order->loadMissing(['shipping', 'customer', 'orderdetails']);
                $capiUser = \App\Support\EcommerceTrackingUser::fromOrder($order);
                if (empty($capiUser['phone']) && $request->phone) {
                    $capiUser['phone'] = $request->phone;
                }
                if (empty($capiUser['name']) && $request->name) {
                    $capiUser['name'] = $request->name;
                }
                $capiUser = \App\Support\EcommerceTrackingUser::forCapi(
                    $capiUser,
                    $_COOKIE['_fbp'] ?? null,
                    $_COOKIE['_fbc'] ?? null
                );

                register_shutdown_function(function () use ($order, $capiUser, $request) {
                    try {
                        $orderDetails = $order->orderdetails ?? \App\Models\Order::with('orderdetails')->find($order->id)?->orderdetails ?? collect();
                        $contentIds  = $orderDetails->pluck('product_id')->map(fn($id) => (string) $id)->values()->toArray();
                        $contents    = $orderDetails->map(fn($i) => [
                            'id'         => (string) $i->product_id,
                            'quantity'   => (int) $i->qty,
                            'item_price' => (float) $i->sale_price,
                        ])->values()->toArray();
                        app(\App\Services\FacebookCapiService::class)->sendEvent('Purchase', [
                            'currency'     => 'BDT',
                            'value'        => $order->amount,
                            'order_id'     => $order->invoice_id ?? $order->id,
                            'content_ids'  => $contentIds,
                            'contents'     => $contents,
                            'num_items'    => count($contents),
                            'content_type' => 'product',
                        ], $capiUser, [
                            'event_id'         => 'purchase_' . ($order->invoice_id ?? $order->id),
                            'event_source_url' => url('customer/order-success/' . $order->id),
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Facebook CAPI Purchase event failed for order ' . $order->id . ': ' . $e->getMessage());
                    }
                });
            } catch (\Exception $e) {
                \Log::error('Facebook CAPI setup failed for order ' . $order->id . ': ' . $e->getMessage());
            }

            Session::forget('coupon_code');
            Session::forget('discount');
            return redirect('customer/order-success/' . $order->id);
        } else {
            Toastr::error('পেমেন্ট মেথড সঠিক নয়।', 'Failed!');
            return redirect()->back();
        }
    }

    public function checkout_resend_otp(Request $request)
    {
        return $this->checkoutOtpResendForChannel($request, 'customer', false);
    }

    public function checkout_cancel_otp(Request $request)
    {
        return $this->checkoutOtpCancelForChannel($request, 'customer');
    }


    public function orders()
    {
        $orders = Order::where('customer_id', Auth::guard('customer')->user()->id)
            ->with(['status', 'orderdetails.product.image', 'orderdetails.image'])
            ->latest()
            ->paginate(10);

        return view('frontEnd.layouts.customer.orders', compact('orders'));
    }

    public function order_success($id)
    {
        $order = Order::with(['orderdetails.size', 'orderdetails.color', 'shipping'])
            ->where('id', $id)
            ->firstOrFail();
        return view('frontEnd.layouts.customer.order_success', compact('order'));
    }

    public function invoice(Request $request)
    {
        $order = Order::where([
            'id' => $request->id,
            'customer_id' => Auth::guard('customer')->user()->id
        ])
            ->with(['orderdetails.size', 'orderdetails.color', 'payment', 'shipping', 'customer'])
            ->firstOrFail();

        return view('frontEnd.layouts.customer.invoice', compact('order'));
    }

    public function order_note(Request $request)
    {
        $order = Order::where([
            'id' => $request->id,
            'customer_id' => Auth::guard('customer')->user()->id
        ])->firstOrFail();

        return view('frontEnd.layouts.customer.order_note', compact('order'));
    }

    public function profile_edit(Request $request)
    {
        $profile_edit = Customer::where(['id' => Auth::guard('customer')->user()->id])->firstOrFail();
        $districts = District::distinct()->select('district')->get();
        $areas = District::where(['district' => $profile_edit->district])->select('area_name', 'id')->get();

        // Refresh the model to get latest data
        $profile_edit->refresh();

        return view('frontEnd.layouts.customer.profile_edit', compact('profile_edit', 'districts', 'areas'));
    }

    public function profile_update(Request $request)
    {
        $update_data = Customer::where(['id' => Auth::guard('customer')->user()->id])->firstOrFail();

        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:customers,email,' . $update_data->id,
            'address' => 'required|string|max:500',
            'district' => 'required|string|max:100',
            'area' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $image = $request->file('image');
        if ($image) {
            try {
                // Delete old image if exists
                if ($update_data->image) {
                    $oldImagePath = public_path($update_data->image);
                    if (file_exists($oldImagePath)) {
                        @unlink($oldImagePath);
                    }
                }

                $name =  time() . '-' . $image->getClientOriginalName();
                $name = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $name);
                $name = strtolower(Str::slug($name));

                // Directory path with public/ prefix
                $uploadpath = 'public/uploads/customer/';
                $uploadFullPath = public_path($uploadpath);

                // Create directory if not exists
                if (!file_exists($uploadFullPath)) {
                    \Illuminate\Support\Facades\File::makeDirectory($uploadFullPath, 0755, true);
                }

                // Full path for saving
                $imageUrl = $uploadFullPath . $name;

                // Process and save image
                $img = Image::make($image->getRealPath());
                $img->encode('webp', 90);
                $img->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $img->save($imageUrl);

                // Verify image was saved
                if (!file_exists($imageUrl)) {
                    throw new \Exception('Image file was not saved successfully');
                }

                // Save path in database (with public/ prefix for asset() helper)
                $imageUrl = $uploadpath . $name;
            } catch (\Exception $e) {
                Toastr::error('Image upload failed: ' . $e->getMessage(), 'Error!');
                return redirect()->back()->withInput();
            }
        } else {
            $imageUrl = $update_data->image;
        }

        $update_data->name = $request->name;
        $update_data->phone = $request->phone;
        $update_data->email = $request->email;
        $update_data->address = $request->address;
        $update_data->district = $request->district;
        $update_data->area = $request->area;
        $update_data->image = $imageUrl;
        $update_data->save();

        // Refresh the model to get updated attributes
        $update_data->refresh();

        Toastr::success('আপনার প্রোফাইল সফলভাবে আপডেট হয়েছে', 'সফল!');
        return redirect()->route('customer.profile_edit');
    }

    public function order_track_result(Request $request)
    {
        $phone = $request->phone;
        $invoice_id = $request->invoice_id;

        // ১. ভ্যালিডেশন: অন্তত একটি ইনপুট থাকতে হবে
        if (!$phone && !$invoice_id) {
            Toastr::error('অনুগ্রহ করে মোবাইল নাম্বার অথবা ইনভয়েস আইডি দিন', 'Error');
            return redirect()->back();
        }

        // ২. কুয়েরি শুরু (Order মডেল ব্যবহার করে)
        $query = Order::query();

        // যদি ইনভয়েস আইডি দেওয়া থাকে
        if ($invoice_id) {
            $query->where('invoice_id', $invoice_id);
        }

        // যদি ফোন নম্বর দেওয়া থাকে
        if ($phone) {
            // আমরা Shipping টেবিল চেক করব কারণ অর্ডারের ফোন নম্বর সেখানেই থাকে
            $query->whereHas('shipping', function ($q) use ($phone) {
                $q->where('phone', $phone);
            });
        }

        // ৩. ডাটা নিয়ে আসা (Eager Loading সহ)
        // latest() দিলে নতুন অর্ডার আগে দেখাবে
        $order = $query->with(['shipping', 'status', 'orderdetails'])->latest()->get();

        // ৪. যদি কোনো অর্ডার না পাওয়া যায়
        if ($order->count() == 0) {
            Toastr::error('দুঃখিত! কোনো অর্ডার পাওয়া যায়নি।', 'Failed');
            return redirect()->back();
        }

        // ৫. ভিউতে ডাটা পাঠানো
        // আপনার কন্ট্রোলারে ভিউয়ের নাম 'tracking_result' দেওয়া আছে, তাই সেটিই রাখলাম।
        // কিন্তু নিশ্চিত হোন আপনার ব্লেড ফাইলের নাম tracking_result.blade.php নাকি track_order.blade.php
        return view('frontEnd.layouts.customer.tracking_result', compact('order'));
    }
    // এই ফাংশনটি মিসিং থাকার কারণেই এরর আসছিল
    public function order_track()
    {
        return view('frontEnd.layouts.customer.order_track');
    }
    public function change_pass()
    {
        return view('frontEnd.layouts.customer.change_password');
    }

    public function password_update(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required_with:new_password|same:new_password|'
        ]);

        $customer = Customer::find(Auth::guard('customer')->user()->id);
        $hashPass = $customer->password;

        if (Hash::check($request->old_password, $hashPass)) {
            $customer->fill([
                'password' => Hash::make($request->new_password)
            ])->save();

            Toastr::success('Success', 'Password changed successfully!');
            return redirect()->route('customer.account');
        } else {
            Toastr::error('Failed', 'Old password not match!');
            return redirect()->back();
        }
    }

    // =====================================
    // ⭐ DIGITAL DOWNLOAD CREATOR (HELPER)
    // =====================================
    private function createDigitalDownloads(Order $order)
    {
        // orderdetails থেকে product_id নিয়ে Product লোড করব
        $items = OrderDetails::where('order_id', $order->id)->get();

        foreach ($items as $item) {
            $product = Product::find($item->product_id);

            if ($product && $product->is_digital == 1 && $product->digital_file) {

                // একই order+product+customer এর জন্য ডুপ্লিকেট না হয়
                DigitalDownload::firstOrCreate(
                    [
                        'order_id'    => $order->id,
                        'product_id'  => $product->id,
                        'customer_id' => $order->customer_id,
                    ],
                    [
                        'token'               => Str::uuid(),
                        'file_path'           => $product->digital_file,
                        'remaining_downloads' => $product->download_limit ?? 5,
                        'expires_at'          => $product->download_expire_days
                            ? now()->addDays($product->download_expire_days)
                            : null,
                    ]
                );
            }
        }
    }
}
