@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

$customer = Auth::guard('customer')->user();
$customerId = $customer->id;

// Site Name & Logo
$siteName = \App\Models\GeneralSetting::first();
$siteInitial = strtoupper(substr($siteName->name ?? 'G', 0, 1));
$siteDisplayName = Str::limit($siteName->name ?? 'GadgetShop', 8);
$generalsetting = $siteName;
$darkLogo = $siteName->dark_logo ?? null;

// Pending Orders Count for Badge
$pendingOrdersCount = \App\Models\Order::where('customer_id', $customerId)
    ->whereNotIn('order_status', ['6', '11'])
    ->count();

// Profile Image - Use direct image path, not accessor
$profileImage = $profile_edit->image ? asset($profile_edit->image) : asset('public/uploads/default/no-image.png');

// Total Order Amount
$totalOrderAmount = \App\Models\Order::where('customer_id', $customerId)->sum('amount');
@endphp

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>সেটিংস | {{ $siteName->name ?? 'Gadget Style' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/assets/css/toastr.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Hind Siliguri', sans-serif; background-color: #F0F2F5; }
        .sidebar-item:hover { background-color: #f3f4f6; color: #4f46e5; }
        .active-menu { background-color: #EEF2FF; color: #4f46e5; border-right: 3px solid #4f46e5; }
        
        /* Mobile Menu Transition */
        #sidebar { transition: transform 0.3s ease-in-out; }
        
        /* Profile Image Upload */
        .profile-image-container {
            position: relative;
            display: inline-block;
        }
        .profile-image-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .profile-image-upload-btn {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 45px;
            height: 45px;
            background: #4f46e5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 3px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
        }
        .profile-image-upload-btn:hover {
            background: #4338ca;
            transform: scale(1.1);
        }
        .profile-image-upload-btn i {
            color: white;
            font-size: 18px;
        }
        #profileImageInput {
            display: none;
        }
        
        /* Select2 Customization */
        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 4px 12px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 34px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
    </style>
</head>
<body class="flex min-h-screen relative">

    <div id="overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r transform -translate-x-full lg:translate-x-0 lg:static lg:inset-auto lg:flex flex-col shrink-0 h-screen transition-transform duration-300">
        <div class="p-4 sm:p-6 flex items-center justify-between lg:justify-start gap-2 border-b border-gray-100">
            @if($darkLogo)
                <a href="{{ route('home') }}" class="flex items-center gap-2 flex-1">
                    <img src="{{ asset($darkLogo) }}" alt="{{ $siteName->name ?? 'Logo' }}" class="h-8 sm:h-10 w-auto max-w-full object-contain">
                </a>
            @else
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold">{{ $siteInitial }}</div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 tracking-tight">{{ $siteDisplayName }}</h1>
                </div>
            @endif
            <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-red-500">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="flex-1 px-0 text-gray-500 font-medium space-y-1 mt-2 overflow-y-auto">
            <a href="{{route('customer.account')}}" class="{{request()->is('customer/account')?'active-menu':'sidebar-item'}} flex items-center px-6 py-3.5 transition-colors">
                <i class="fas fa-home w-6"></i> ড্যাশবোর্ড
            </a>
            <a href="{{route('customer.orders')}}" class="{{request()->is('customer/orders')?'active-menu':'sidebar-item'}} flex items-center px-6 py-3.5 transition-colors">
                <i class="fas fa-box-open w-6"></i> আমার অর্ডার 
                @if($pendingOrdersCount > 0)
                    <span class="ml-auto bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full">{{ $pendingOrdersCount }}</span>
                @endif
            </a>
            <a href="{{route('customer.order_track')}}" class="{{request()->is('customer/order-track*')?'active-menu':'sidebar-item'}} flex items-center px-6 py-3.5 transition-colors">
                <i class="fas fa-truck w-6"></i> ট্র্যাক অর্ডার
            </a>
            <a href="{{route('customer.refunds')}}" class="{{request()->is('customer/refunds*')?'active-menu':'sidebar-item'}} flex items-center px-6 py-3.5 transition-colors">
                <i class="fas fa-undo w-6"></i> রিফান্ড রিকোয়েস্ট
            </a>
            <a href="{{ route('complaint') }}" class="{{ request()->is('complaint') ? 'active-menu' : 'sidebar-item' }} flex items-center px-6 py-3.5 transition-colors">
                <i class="fas fa-headset w-6"></i> সাপোর্ট টিকেট
            </a>
            <a href="{{route('customer.profile_edit')}}" class="{{request()->is('customer/profile-edit')?'active-menu':'sidebar-item'}} flex items-center px-6 py-3.5 transition-colors">
                <i class="fas fa-user-cog w-6"></i> সেটিংস
            </a>
        </nav>

        <div class="p-6 border-t">
            <a href="{{ route('customer.logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="w-full flex items-center justify-center px-4 py-2.5 text-red-500 bg-red-50 hover:bg-red-100 rounded-lg font-bold transition">
                <i class="fas fa-sign-out-alt mr-2"></i> লগআউট
            </a>
            <form id="logout-form" action="{{ route('customer.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto h-screen w-full">
        
        <header class="bg-white px-6 lg:px-8 py-4 flex justify-between items-center sticky top-0 z-20 shadow-sm border-b">
            <div class="lg:hidden mr-4">
                <button onclick="toggleSidebar()" class="text-gray-600 text-xl p-2"><i class="fas fa-bars"></i></button>
            </div>

            <div class="flex-1">
                <h2 class="text-xl font-bold text-gray-800">সেটিংস</h2>
                <p class="text-xs text-gray-400 mt-0.5 hidden sm:block">আপনার প্রোফাইল তথ্য আপডেট করুন</p>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden sm:flex bg-green-50 text-green-700 px-4 py-2 rounded-full items-center font-bold text-sm border border-green-100">
                    <i class="fas fa-wallet mr-2"></i> মোট: ৳{{ number_format($totalOrderAmount, 0) }}
                </div>
                
                <div class="relative cursor-pointer w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center hover:bg-gray-100 transition">
                    <i class="far fa-bell text-gray-600"></i>
                </div>

                <img src="{{ $profileImage }}" onerror="this.src='{{ asset('public/uploads/default/no-image.png') }}'" class="w-10 h-10 rounded-full border-2 border-white shadow-sm cursor-pointer" alt="Profile">
            </div>
        </header>

        <div class="p-4 lg:p-8 max-w-4xl mx-auto">
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50">
                    <h3 class="text-lg font-bold text-gray-800">⚙️ প্রোফাইল আপডেট</h3>
                </div>
                
                <form action="{{route('customer.profile_update')}}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6" id="profileForm">
                    @csrf
                    
                    {{-- Profile Image Upload Section --}}
                    <div class="flex flex-col items-center mb-6 pb-6 border-b border-gray-100">
                        <div class="profile-image-container mb-4">
                            <img id="profileImagePreview" src="{{ $profileImage }}" onerror="this.src='{{ asset('public/uploads/default/no-image.png') }}'" class="profile-image-preview" alt="Profile Image">
                            <label for="profileImageInput" class="profile-image-upload-btn">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" id="profileImageInput" name="image" accept="image/jpeg,image/jpg,image/png,image/webp" onchange="previewProfileImage(this)">
                        </div>
                        <div class="text-center">
                            <h6 class="font-bold text-gray-800 mb-1">প্রোফাইল ছবি</h6>
                            <p class="text-xs text-gray-500">PNG, JPG বা WEBP (সর্বোচ্চ 2MB)</p>
                            <p id="imageFileName" class="text-xs text-indigo-600 mt-1 hidden"></p>
                            @if(session('success'))
                                <p class="text-green-500 text-xs mt-1">{{ session('success') }}</p>
                            @endif
                            @error('image')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Form Fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Full Name --}}
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">পুরো নাম *</label>
                            <input type="text" id="name" name="name" value="{{old('name', $profile_edit->name)}}" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Phone Number --}}
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">ফোন নম্বর *</label>
                            <input type="number" id="phone" name="phone" value="{{old('phone', $profile_edit->phone)}}" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('phone') border-red-500 @enderror">
                                @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                        </div>

                        {{-- Email Address --}}
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">ইমেইল ঠিকানা *</label>
                            <input type="email" id="email" name="email" value="{{old('email', $profile_edit->email)}}" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('email') border-red-500 @enderror">
                                @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                        </div>

                        {{-- Address --}}
                        <div>
                            <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">ঠিকানা *</label>
                            <input type="text" id="address" name="address" value="{{old('address', $profile_edit->address)}}" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('address') border-red-500 @enderror">
                                @error('address')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                        </div>

                        {{-- District --}}
                        <div>
                            <label for="district" class="block text-sm font-semibold text-gray-700 mb-2">জেলা *</label>
                            <select id="district" name="district" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition select2 @error('district') border-red-500 @enderror" required>
                                <option value="">নির্বাচন করুন...</option>
                                    @foreach($districts as $key=>$district)
                                    <option value="{{$district->district}}" @if(old('district', $profile_edit->district)==$district->district) selected @endif>{{$district->district}}</option>
                                    @endforeach
                                </select>
                                @error('district')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                        </div>

                        {{-- Area --}}
                        <div>
                            <label for="area" class="block text-sm font-semibold text-gray-700 mb-2">এলাকা *</label>
                            <select id="area" name="area" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition select2 area @error('area') border-red-500 @enderror" required>
                                <option value="">নির্বাচন করুন...</option>
                                    @foreach($areas as $key=>$area)
                                    <option value="{{$area->id}}" @if(old('area', $profile_edit->area) == $area->id) selected @endif>{{$area->area_name}}</option>
                                    @endforeach
                                </select>
                                @error('area')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                        </div>
                            </div>

                    {{-- Submit Button --}}
                    <div class="pt-6 border-t border-gray-100">
                        <button type="submit" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-8 py-3 rounded-lg transition duration-200 shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            আপডেট করুন
                        </button>
                        </div>
                    </form>
            </div>

        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Toastr JS -->
    <script src="{{asset('public/backEnd/')}}/assets/js/toastr.min.js"></script>
    {!! Toastr::message() !!}
    <script>
        // Toastr Configuration
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Display session messages
        @if(Session::has('success'))
            toastr.success("{{ Session::get('success') }}", "সফল!");
        @endif
        @if(Session::has('error'))
            toastr.error("{{ Session::get('error') }}", "ত্রুটি!");
        @endif
        @if(Session::has('info'))
            toastr.info("{{ Session::get('info') }}", "তথ্য!");
        @endif
        @if(Session::has('warning'))
            toastr.warning("{{ Session::get('warning') }}", "সতর্কতা!");
        @endif
    </script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        // Profile Image Preview
        function previewProfileImage(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                // Check file size
                if (file.size > maxSize) {
                    alert('ইমেজ সাইজ 2MB এর বেশি হতে পারবে না!');
                    input.value = '';
                    return;
                }
                
                // Check file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('শুধুমাত্র JPG, PNG বা WEBP ফরম্যাটের ইমেজ আপলোড করা যাবে!');
                    input.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profileImagePreview').src = e.target.result;
                    document.getElementById('imageFileName').textContent = file.name;
                    document.getElementById('imageFileName').classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }
        
        // Form submission validation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const imageInput = document.getElementById('profileImageInput');
            if (imageInput.files && imageInput.files[0]) {
                const file = imageInput.files[0];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (file.size > maxSize) {
                    e.preventDefault();
                    alert('ইমেজ সাইজ 2MB এর বেশি হতে পারবে না!');
                    return false;
                }
            }
        });

        // Select2 Initialization
    $(document).ready(function() {
            $('.select2').select2({
                theme: 'default',
                width: '100%'
            });
    });

        // District Change Handler
        $('.district').on('change', function(){
    var id = $(this).val();
        $.ajax({
                type: "GET",
                data: {'id': id},
                url: "{{route('districts')}}",
                success: function(res){               
            if(res){
                $(".area").empty();
                        $(".area").append('<option value="">নির্বাচন করুন...</option>');
                        $.each(res, function(key, value){
                            $(".area").append('<option value="'+key+'">'+value+'</option>');
                        });
                    } else {
               $(".area").empty();
            }
           }
        });  
   });
</script>
</body>
</html>
