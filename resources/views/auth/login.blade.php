@extends('auth.master')

@section('title')
    {{__('login')}}
@endsection

@section('mainContent')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

body, html {
    margin: 0; padding: 0;
    width: 100%; height: 100%;
    font-family: 'Inter', sans-serif;
    background-color: #ffffff;
    overflow-x: hidden;
}

/* --- SPLIT LAYOUT --- */
.split-layout {
    display: flex;
    min-height: 100vh;
    width: 100vw;
}

/* --- LEFT SIDE (HERO) --- */
.split-left {
    width: 50%;
    background-color: #042f1a; /* Very dark rich green */
    display: flex;
    flex-direction: column;
    padding: 60px 80px;
    position: relative;
    overflow: hidden;
}


.brand-logo { margin-bottom: 40px; display: flex; justify-content: center; width: 100%; }
.brand-logo a { display: inline-block; }
.brand-logo img { height: 60px; filter: brightness(0) invert(1);  margin: 0 auto; } /* Force logo to white */

.hero-title {
    font-size: 42px;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.1;
    margin: 0 0 20px 0;
    letter-spacing: -1px;
}
.hero-subtitle {
    font-size: 16px;
    color: #a7f3d0; /* Light green text */
    line-height: 1.5;
    margin: 0 0 60px 0;
    max-width: 400px;
}

/* Abstract Route Graphic */
.hero-graphic {
    position: relative;
    flex-grow: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 20px;
}

/* Subtle Grid Background */
.grid-bg {
    position: absolute;
    inset: 0;
    background-image: 
        linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px);
    background-size: 30px 30px;
    z-index: 1;
}

.route-svg {
    position: relative;
    z-index: 2;
    width: 100%;
    max-width: 400px;
    height: 250px;
    overflow: visible;
}
.route-path {
    fill: none;
    stroke: #10b981; /* Bright green */
    stroke-width: 3;
    stroke-linecap: round;
    stroke-dasharray: 1000;
    stroke-dashoffset: 1000;
    animation: drawRoute 3s ease forwards;
}
@keyframes drawRoute { to { stroke-dashoffset: 0; } }

.route-point { animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; opacity: 0; transform-origin: center; }
.p-start { animation-delay: 0.5s; }
.p-end { animation-delay: 2.5s; }
@keyframes popIn { 0% { transform: scale(0); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }

.route-text { font-size: 13px; font-weight: 500; fill: #d1fae5; animation: fadeIn 1s forwards; opacity: 0; }
.t-start { animation-delay: 1s; }
.t-end { animation-delay: 3s; }
@keyframes fadeIn { to { opacity: 1; } }

/* Floating Status Card */
.status-card {
    position: absolute;
    bottom: 50px;
    left: 80px;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    width: 320px;
    z-index: 10;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    animation: floatUp 1s ease forwards;
    transform: translateY(20px);
    opacity: 0;
}
@keyframes floatUp { to { transform: translateY(0); opacity: 1; } }

.sc-icon {
    width: 40px; height: 40px;
    background: #10b981;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    color: white;
}
.sc-details h4 { margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #fff; }
.sc-details p { margin: 0 0 10px 0; font-size: 12px; color: #a7f3d0; }
.progress-bar { width: 100%; height: 4px; background: rgba(255,255,255,0.2); border-radius: 2px; overflow: hidden; }
.progress-fill { width: 70%; height: 100%; background: #f59e0b; border-radius: 2px; }


/* --- RIGHT SIDE (FORM) --- */
.split-right {
    width: 50%;
    background-color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
}
.form-wrapper {
    width: 100%;
    max-width: 380px;
}

.form-header { margin-bottom: 35px; }
.form-header h2 { font-size: 28px; font-weight: 700; color: #111827; margin: 0 0 8px 0; letter-spacing: -0.5px; }
.form-header p { font-size: 15px; color: #6b7280; margin: 0; }

.custom-input-wrap { margin-bottom: 20px; display: block; }
.input-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px; }
.input-with-icon {
    position: relative;
    display: flex;
    align-items: center;
}
.input-icon {
    position: absolute;
    left: 16px;
    color: #9ca3af;
    width: 20px; height: 20px;
    pointer-events: none;
}
.premium-input {
    width: 100%;
    height: 50px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 0 16px 0 46px; /* Space for icon */
    font-size: 14px;
    color: #111827;
    font-family: 'Inter', sans-serif;
    transition: all 0.2s;
}
.premium-input::placeholder { color: #9ca3af; }
.premium-input:focus {
    outline: none;
    background: #ffffff;
    border-color: #006837;
    box-shadow: 0 0 0 3px rgba(0, 104, 55, 0.1);
}

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    margin-top: 10px;
}
.custom-checkbox { display: flex; align-items: center; gap: 8px; }
.custom-checkbox input[type="checkbox"] {
    appearance: none; width: 16px; height: 16px;
    border: 1px solid #d1d5db; border-radius: 4px;
    cursor: pointer; position: relative; background: #fff; transition: 0.2s;
}
.custom-checkbox input[type="checkbox"]:checked { background: #006837; border-color: #006837; }
.custom-checkbox input[type="checkbox"]:checked::after {
    content: '✔'; position: absolute; color: white; font-size: 10px; left: 3px; top: 1px;
}
.custom-checkbox label { font-size: 13px; color: #4b5563; cursor: pointer; margin: 0; font-weight: 500; }
.forgot-link { font-size: 13px; font-weight: 600; color: #006837; text-decoration: none; }
.forgot-link:hover { text-decoration: underline; }

.btn-solid-green {
    width: 100%;
    max-width: 220px;
    height: 44px;
    margin: 0 auto;
    display: block;
    background: linear-gradient(to bottom, #10b981 0%, #006837 100%);
    color: #ffffff;
    border: none;
    border-radius: 25px; /* Pill shape */
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
    box-shadow: 
        inset 0px 2px 4px rgba(255, 255, 255, 0.6), 
        inset 0px -3px 5px rgba(0, 50, 25, 0.6), 
        0px 6px 12px rgba(0, 0, 0, 0.2), 
        0px 4px 0px #004020; 
    text-shadow: 0 -1px 2px rgba(0,0,0,0.5); 
    transition: all 0.15s;
    position: relative;
    top: 0;
}
.btn-solid-green:hover { 
    background: linear-gradient(to bottom, #13c98c 0%, #007a41 100%); 
}
.btn-solid-green:active { 
    top: 5px; /* Push down effect */
    box-shadow: 
        inset 0px 1px 2px rgba(255, 255, 255, 0.4), 
        inset 0px -2px 4px rgba(0, 50, 25, 0.6), 
        0px 2px 5px rgba(0, 0, 0, 0.2), 
        0px 0px 0px #004020; 
}

.signup-text { margin-top: 30px; text-align: center; font-size: 14px; color: #6b7280; }
.signup-text a { color: #006837; font-weight: 600; text-decoration: none; }
.signup-text a:hover { text-decoration: underline; }

/* Demo Pills */
.demo-links { margin-top: 30px; text-align: center; padding-top: 20px; border-top: 1px solid #f3f4f6; }
.demo-links a { font-size: 11px; display: inline-block; background: #f3f4f6; padding: 6px 10px; margin: 3px; border-radius: 6px; color: #4b5563; font-weight: 500; cursor: pointer; transition: 0.2s; }
.demo-links a:hover { background: #006837; color: #fff; }

/* MOBILE RESPONSIVE */
@media (max-width: 991px) {
    .split-layout { flex-direction: column; }
    .split-left { display: none !important; } /* Hide hero graphic entirely on mobile */
    .split-right { 
        width: 100%; 
        min-height: 100vh; 
        padding: 20px; 
        align-items: center; 
        justify-content: center; 
        background-color: #f9fafb; /* Slight gray background so the white form card pops */
    }
    .form-wrapper { 
        max-width: 420px; 
        width: 100%; 
        margin: 0 auto; 
        background: #ffffff;
        padding: 40px 30px;
        border: 1px solid #e5e7eb; /* Added border */
        border-radius: 16px;
        box-shadow: 0 15px 30px rgba(0,0,0,0.05); /* Soft premium shadow */
    }
    .mobile-logo { display: block !important; margin-bottom: 30px; text-align: center; width: 100%; }
    .mobile-logo img { margin: 0 auto; display: inline-block; }
    .form-header { text-align: center; } /* Center text on mobile */
}

</style>

<div class="split-layout">
    
    <!-- LEFT SIDE: HERO GRAPHIC -->
    <div class="split-left">
        <div class="grid-bg"></div>
        
        <div style="position: relative; z-index: 10; width: 100%; max-width: 450px; margin: 0 auto; display: flex; flex-direction: column; height: 100%;">
            
            <div class="brand-logo">
                <a href="{{ url('/') }}">
                    <img src="{{ setting('dark_logo') && @is_file_exists(setting('dark_logo')['original_image']) ? get_media(setting('dark_logo')['original_image']) : get_media('images/default/logo/logo_dark.png') }}" alt="Logo">
                </a>
            </div>
            
            <h1 class="hero-title">Every parcel, tracked<br>from pickup to<br>doorstep.</h1>
            <p class="hero-subtitle">Book deliveries, follow their route and manage your shipments from one place.</p>
            
            <div class="hero-graphic">
                <svg viewBox="0 0 300 200" class="route-svg">
                    <!-- Curved Route Line -->
                    <path class="route-path" d="M 20 160 C 100 160, 150 60, 260 40"></path>
                    
                    <!-- Start Point -->
                    <circle cx="20" cy="160" r="5" fill="#ffffff" stroke="#10b981" stroke-width="2" class="route-point p-start"></circle>
                    <!-- End Point -->
                    <circle cx="260" cy="40" r="5" fill="#f59e0b" stroke="#ffffff" stroke-width="2" class="route-point p-end"></circle>
                    
                    <!-- Text Labels -->
                    <text x="15" y="180" class="route-text t-start">Dhaka</text>
                    <text x="235" y="25" class="route-text t-end">Chattogram</text>
                </svg>
            </div>
            
            <!-- Floating Status Card -->
            <div class="status-card">
                <div class="sc-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                </div>
                <div class="sc-details">
                    <h4>Parcel on transit</h4>
                    <p>Out for delivery in Chattogram</p>
                    <div class="progress-bar"><div class="progress-fill"></div></div>
                </div>
            </div>
            
        </div>
    </div>
    
    <!-- RIGHT SIDE: FORM -->
    <div class="split-right">
        <div class="form-wrapper">
            <!-- Mobile Logo (Hidden on desktop) -->
            <div class="mobile-logo" style="display: none; margin-bottom: 30px; text-align: center;">
                <a href="{{ url('/') }}">
                    <img src="{{ setting('dark_logo') && @is_file_exists(setting('dark_logo')['original_image']) ? get_media(setting('dark_logo')['original_image']) : get_media('images/default/logo/logo_dark.png') }}" alt="Logo" style="height: 40px;">
                </a>
            </div>
            
            <div class="form-header">
                <h2>Welcome back</h2>
                <p>Log in to manage your shipments.</p>
            </div>
            
            <form class="ajax_form" method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="custom-input-wrap">
                    <label class="input-label" for="email">Email</label>
                    <div class="input-with-icon">
                        <!-- Email Icon -->
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        
                        <input type="email" name="email" id="email" class="premium-input" value="{{ old('email') }}" placeholder="you@example.com" required>
                    </div>
                    @if($errors->has('email'))
                        <div class="text-danger mt-1" style="font-size: 12px; color: #ef4444;">{{ $errors->first('email') }}</div>
                    @endif
                </div>
                
                <div class="custom-input-wrap">
                    <label class="input-label" for="password">Password</label>
                    <div class="input-with-icon">
                        <!-- Lock Icon -->
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        
                        <input type="password" name="password" id="password" class="premium-input" placeholder="Enter your password" required>
                    </div>
                    @if($errors->has('password'))
                        <div class="text-danger mt-1" style="font-size: 12px; color: #ef4444;">{{ $errors->first('password') }}</div>
                    @endif
                </div>
                
                <div class="form-options">
                    <div class="custom-checkbox">
                        <input type="checkbox" id="remember_me" name="remember_me" checked>
                        <label for="remember_me">Remember Me</label>
                    </div>
                    <a href="{{route('forgot-password')}}" class="forgot-link">Forgot password?</a>
                </div>
                
                @if (setting('is_recaptcha_activated') && setting('recaptcha_site_key'))
                    <div class="custom-input-wrap mb-4">
                        <div id="html_element" class="g-recaptcha" data-sitekey="{{setting('recaptcha_site_key')}}"></div>
                    </div>
                @endif

                <button type="submit" class="btn-solid-green">
                    Log In
                </button>
            </form>
            
            <div class="signup-text">
                Don't have an account? <a href="{{ route('register') }}">Create an account</a>
            </div>
            
            @if(isDemoMode())
                <div class="demo-links">
                    <a class="input_filler" data-type="admin">Admin</a>
                    <a class="input_filler" data-type="finance">Finance</a>
                    <a class="input_filler" data-type="branch_manager">Branch</a>
                    <a class="input_filler" data-type="merchant">Merchant</a>
                    <a class="input_filler" data-type="merchant_staff">Staff</a>
                </div>
            @endif

        </div>
    </div>
    
</div>
@endsection

@push('script')
    @if (setting('is_recaptcha_activated') && setting('recaptcha_site_key'))
        <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
        <script type="text/javascript">
            var onloadCallback = function() {
                grecaptcha.render('html_element', {
                    'sitekey' : '{{setting('recaptcha_site_key')}}',
                    'size' : 'md'
                });
            };
        </script>
    @endif
    
    @if ($errors->any())
        <script>
            @foreach ($errors->all() as $error)
                toastr.error('{{ $error }}');
            @endforeach
        </script>
    @endif

    <script>
        $(document).ready(function () {
            // Demo Login Fillers
            $(document).on('click', '.input_filler', function () {
                var type = $(this).data('type');
                if (type == 'admin') {
                    $('#email').val('admin@spagreen.net');
                    $('#password').val('123456');
                } else if (type == 'branch_manager') {
                    $('#email').val('staff_3@spagreen.net');
                    $('#password').val('123456');
                } else if (type == 'finance') {
                    $('#email').val('staff_12@spagreen.net');
                    $('#password').val('123456');
                } else if (type == 'merchant') {
                    $('#email').val('merchant@spagreen.net');
                    $('#password').val('123456');
                } else if (type == 'merchant_staff') {
                    $('#email').val('mehedi@spagreen.net');
                    $('#password').val('123456');
                }
            });
        });
    </script>
@endpush
