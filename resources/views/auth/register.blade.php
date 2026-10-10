@extends('auth.master')

@section('title')
    {{ __('merchant_registration') }}
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

/* --- LEFT SIDE (HERO STICKY) --- */
.split-left {
    width: 50%;
    background-color: #042f1a; /* Very dark rich green */
    display: flex;
    flex-direction: column;
    padding: 60px 80px;
    position: sticky;
    top: 0;
    height: 100vh;
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
    margin: 0 0px 20px 0;
    letter-spacing: -1px;
}
.hero-subtitle {
    font-size: 16px;
    color: #a7f3d0; /* Light green text */
    line-height: 1.5;
    margin: 0 0 40px 0;
    max-width: 400px;
}

/* Abstract Route Graphic */
.hero-graphic {
    position: relative;
    flex-grow: 1;
    display: flex;
    align-items: center;
    justify-content: center;
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
    justify-content: center;
    padding: 60px 40px;
}
.form-wrapper {
    width: 100%;
    max-width: 460px;
    margin: 0 auto;
}

.form-header { margin-bottom: 35px; }
.form-header h2 { font-size: 28px; font-weight: 700; color: #111827; margin: 0 0 8px 0; letter-spacing: -0.5px; }
.form-header p { font-size: 15px; color: #6b7280; margin: 0; }

.custom-input-wrap { margin-bottom: 20px; display: block; }
.input-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px; }

.premium-input {
    width: 100%;
    height: 50px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 0 16px;
    font-size: 14px;
    color: #111827;
    font-family: 'Inter', sans-serif;
    transition: all 0.2s;
}
textarea.premium-input { height: auto; padding: 12px 16px; }
select.premium-input { appearance: auto; }

.premium-input::placeholder { color: #9ca3af; }
.premium-input:focus {
    outline: none;
    background: #ffffff;
    border-color: #006837;
    box-shadow: 0 0 0 3px rgba(0, 104, 55, 0.1);
}

/* Radio Cards for Account Type */
.account-type-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 25px;
}
.account-radio { display: none; }
.account-card {
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    border-radius: 10px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px; /* Space between icon and text */
    cursor: pointer;
    transition: all 0.2s;
}
.account-card h5 { margin: 0; font-size: 15px; font-weight: 600; color: #374151; }
.account-card svg { color: #9ca3af; transition: 0.2s; width: 22px; height: 22px; }

.account-radio:checked + .account-card {
    border-color: #006837;
    background: #f0fdf4;
    box-shadow: 0 4px 12px rgba(0, 104, 55, 0.08);
}
.account-radio:checked + .account-card h5 { color: #006837; }
.account-radio:checked + .account-card svg { color: #006837; }

/* Grid row for inputs */
.input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }

.custom-checkbox { display: flex; align-items: flex-start; gap: 8px; margin-bottom: 25px; }
.custom-checkbox input[type="checkbox"] {
    appearance: none; width: 18px; height: 18px; min-width: 18px;
    border: 1px solid #d1d5db; border-radius: 4px; margin-top: 2px;
    cursor: pointer; position: relative; background: #fff; transition: 0.2s;
}
.custom-checkbox input[type="checkbox"]:checked { background: #006837; border-color: #006837; }
.custom-checkbox input[type="checkbox"]:checked::after {
    content: '✔'; position: absolute; color: white; font-size: 11px; left: 3px; top: 1px;
}
.custom-checkbox label { font-size: 13px; color: #4b5563; cursor: pointer; margin: 0; line-height: 1.5; font-weight: 500; }
.custom-checkbox label a { color: #006837; font-weight: 600; text-decoration: none; }

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

.login-text { margin-top: 30px; text-align: center; font-size: 14px; color: #6b7280; }
.login-text a { color: #006837; font-weight: 600; text-decoration: none; }
.login-text a:hover { text-decoration: underline; }

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
        max-width: 480px;
        width: 100%;
        margin: 0 auto;
        background: #ffffff;
        padding: 30px 20px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 15px 30px rgba(0,0,0,0.05);
    }
    .mobile-logo { display: block !important; margin-bottom: 25px; text-align: center; width: 100%; }
    .mobile-logo img { margin: 0 auto; display: inline-block; }
    .form-header { text-align: center; }
    .input-row { grid-template-columns: 1fr; gap: 0; }
}

</style>

<div class="split-layout">

    <!-- LEFT SIDE: HERO GRAPHIC (Sticky) -->
    <div class="split-left">
        <div class="grid-bg"></div>

        <div style="position: relative; z-index: 10; width: 100%; max-width: 450px; margin: 0 auto; display: flex; flex-direction: column; height: 100%;">

            <div class="brand-logo ">
                <a href="{{ url('/') }}">
                    <img src="{{ setting('dark_logo') && @is_file_exists(setting('dark_logo')['original_image']) ? get_media(setting('dark_logo')['original_image']) : get_media('images/default/logo/logo_dark.png') }}" alt="Logo">
                </a>
            </div>

            <h1 class="hero-title">Join our global logistics network.</h1>
            <p class="hero-subtitle">Create a merchant account today to seamlessly book deliveries, manage shipments, and scale your business globally.</p>

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
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <div class="sc-details">
                    <h4>Trusted by 10,000+</h4>
                    <p>Merchants using our platform daily</p>
                    <div class="progress-bar"><div class="progress-fill" style="width: 100%; background: #10b981;"></div></div>
                </div>
            </div>

        </div>
    </div>

    <!-- RIGHT SIDE: REGISTRATION FORM -->
    <div class="split-right">
        <div class="form-wrapper">
            <!-- Mobile Logo -->
            <div class="mobile-logo" style="display: none;">
                <a href="{{ url('/') }}">
                    <img src="{{ setting('dark_logo') && @is_file_exists(setting('dark_logo')['original_image']) ? get_media(setting('dark_logo')['original_image']) : get_media('images/default/logo/logo_dark.png') }}" alt="Logo" style="height: 40px;">
                </a>
            </div>

            <div class="form-header">
                <h2>Create Account</h2>
                <p>Register as a new merchant to get started.</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="custom-input-wrap">
                    <label class="input-label">Account Type</label>
                    <div class="account-type-grid">
                        <label>
                            <input type="radio" name="is_personal" class="account-radio" value="0" checked>
                            <div class="account-card">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9v11a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9"/><path d="M22 9H2l1.66-5.83A2 2 0 0 1 5.58 2h12.84a2 2 0 0 1 1.92 1.17L22 9z"/><path d="M12 22v-9"/></svg>
                                <h5>Merchant</h5>
                            </div>
                        </label>
                        <label>
                            <input type="radio" name="is_personal" class="account-radio" value="1">
                            <div class="account-card">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <h5>Personal</h5>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="custom-input-wrap" id="company_input_wrap">
                    <label class="input-label" for="company">{{ __('company_name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="company" id="company" value="{{ old('company') }}" class="premium-input" placeholder="Your Company Name" required>
                    @if ($errors->has('company'))
                        <div class="text-danger mt-1" style="font-size: 12px; color: #ef4444;">{{ $errors->first('company') }}</div>
                    @endif
                </div>

                <div class="input-row">
                    <div class="custom-input-wrap">
                        <label class="input-label" for="first_name">{{ __('first_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" class="premium-input" placeholder="First Name" required>
                        @if ($errors->has('first_name'))
                            <div class="text-danger mt-1" style="font-size: 12px; color: #ef4444;">{{ $errors->first('first_name') }}</div>
                        @endif
                    </div>
                    <div class="custom-input-wrap">
                        <label class="input-label" for="last_name">{{ __('last_name') }}</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" class="premium-input" placeholder="Last Name">
                        @if ($errors->has('last_name'))
                            <div class="text-danger mt-1" style="font-size: 12px; color: #ef4444;">{{ $errors->first('last_name') }}</div>
                        @endif
                    </div>
                </div>

                <div class="custom-input-wrap">
                    <label class="input-label" for="branch_id">{{ __('Hub') }} <span class="text-danger">*</span></label>
                    <select name="branch_id" id="branch_id" class="premium-input" required>
                        <option value="">{{ __('select_hub') }}</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('branch_id'))
                        <div class="text-danger mt-1" style="font-size: 12px; color: #ef4444;">{{ $errors->first('branch_id') }}</div>
                    @endif
                </div>

                <div class="custom-input-wrap">
                    <label class="input-label" for="address">{{ __('address') }}</label>
                    <textarea class="premium-input" id="address" name="address" rows="2" placeholder="Full Address">{{ old('address') }}</textarea>
                    @if ($errors->has('address'))
                        <div class="text-danger mt-1" style="font-size: 12px; color: #ef4444;">{{ $errors->first('address') }}</div>
                    @endif
                </div>

                <div class="custom-input-wrap">
                    <label class="input-label" for="phone_number">{{ __('phone') }} <span class="text-danger">*</span></label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" class="premium-input" placeholder="+880 1XXX-XXXXXX" required>
                    @if ($errors->has('phone_number'))
                        <div class="text-danger mt-1" style="font-size: 12px; color: #ef4444;">{{ $errors->first('phone_number') }}</div>
                    @endif
                </div>

                <div class="custom-input-wrap">
                    <label class="input-label" for="email">{{ __('email') }} <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="premium-input" placeholder="you@example.com" required>
                    @if ($errors->has('email'))
                        <div class="text-danger mt-1" style="font-size: 12px; color: #ef4444;">{{ $errors->first('email') }}</div>
                    @endif
                </div>

                <div class="custom-input-wrap">
                    <label class="input-label" for="password">{{ __('password') }} <span class="text-danger">*</span></label>
                    <input type="password" name="password" id="password" class="premium-input" placeholder="Enter secure password" required>
                    @if ($errors->has('password'))
                        <div class="text-danger mt-1" style="font-size: 12px; color: #ef4444;">{{ $errors->first('password') }}</div>
                    @endif
                </div>

                @if (setting('is_recaptcha_activated') && setting('recaptcha_site_key'))
                    <div class="custom-input-wrap mb-4">
                        <div id="html_element" class="g-recaptcha" data-sitekey="{{setting('recaptcha_site_key')}}"></div>
                        @if ($errors->has('g-recaptcha-response'))
                            <div class="text-danger mt-1" style="font-size: 12px; color: #ef4444;">{{ $errors->first('g-recaptcha-response') }}</div>
                        @endif
                    </div>
                @endif

                <div class="custom-checkbox">
                    <input type="checkbox" name="agree_with" id="checkbox_agree" value="1" checked required>
                    <label for="checkbox_agree">
                        {{ __('i_agree') }}
                        <a target="_blank" href="{{ url('/privacy-policy') }}">{{ __('privacy_policy') }}</a> &amp;
                        <a target="_blank" href="{{ url('/terms-condition') }}">{{ __('terms') }}</a>
                    </label>
                </div>

                <button type="submit" class="btn-solid-green">
                    {{ __('register') }}
                </button>
            </form>

            <div class="login-text">
                {{ __('already_have_an_account') }} <a href="{{ route('login') }}">{{ __('sign_in_instead') }}</a>
            </div>

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
        document.addEventListener('DOMContentLoaded', function() {
            const radioButtons = document.querySelectorAll('input[name="is_personal"]');
            const companyWrap = document.getElementById('company_input_wrap');
            const companyInput = document.getElementById('company');

            function toggleCompany() {
                const isPersonal = document.querySelector('input[name="is_personal"]:checked').value;
                if (isPersonal === '1') {
                    companyWrap.style.display = 'none';
                    companyInput.removeAttribute('required');
                } else {
                    companyWrap.style.display = 'block';
                    companyInput.setAttribute('required', 'required');
                }
            }

            radioButtons.forEach(radio => radio.addEventListener('change', toggleCompany));
            toggleCompany(); // Check on initial load
        });
    </script>
@endpush
