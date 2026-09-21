@extends('backend.layouts.base')
@push('css')
<style>
    @media (min-width: 992px) {
        .header-position {
            width: 20% !important;
        }
        .main-wrapper {
            padding-left: 20% !important;
        }
        .homepageFixBTN {
            width: 80% !important;
        }
    }
</style>
@endpush
@section('base.content')
    @php
        $user = Sentinel::getUser();
    @endphp
    @if($user && ($user->user_type == 'merchant' || $user->user_type == 'merchant_staff'))
        @include('backend.layouts.merchant_sidebar')
      @elseif($user && ($user->user_type == 'delivery_man' || $user->user_type == 'delivery'))
        @include('backend.layouts.deliveryman_sidebar')
        @else
        @include('backend.layouts.sidebar')
    @endif
    <main class="main-wrapper">
        @include('backend.layouts.header')
        <div class="main-content-wrapper">
            @yield('mainContent')
        </div>
    </main>
    @include('backend.layouts.footer')
@endsection