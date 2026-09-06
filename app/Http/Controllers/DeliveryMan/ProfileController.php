<?php

namespace App\Http\Controllers\DeliveryMan;

use App\Http\Controllers\Controller;
use App\Repositories\DeliveryMan\ProfileRepository;
use Brian2694\Toastr\Facades\Toastr;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    protected $profileRepo;

    public function __construct(ProfileRepository $profileRepo)
    {
        $this->profileRepo = $profileRepo;
    }

    public function index()
    {
        $authUser = Sentinel::getUser();
        if (!$authUser) {
            return redirect()->route('login');
        }
        $user = \App\Models\User::find($authUser->id) ?? $authUser;
        $deliveryMan = $this->profileRepo->getProfile();

        return view('deliveryman.profile.index', compact('user', 'deliveryMan'));
    }

    public function updateProfile(Request $request)
    {
        $user = Sentinel::getUser();
        if (!$user) {
            Toastr::error(__('unauthorized'));
            return redirect()->route('login');
        }

        $request->validate([
            'first_name'      => 'required|max:50',
            'last_name'       => 'required|max:50',
            'email'           => 'required|email|unique:users,email,' . $user->id,
            'phone_number'    => 'required|min:6',
            'address'         => 'nullable|string',
            'city'            => 'nullable|string|max:100',
            'zip'             => 'nullable|string|max:20',
            'image'           => 'nullable|mimes:jpg,jpeg,png,webp|max:5120',
            'driving_license' => 'nullable|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        try {
            $this->profileRepo->updateProfile($request);
            Toastr::success(__('profile_updated_successfully'));
            return back();
        } catch (\Throwable $e) {
            Log::error('DeliveryMan Profile update error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            Toastr::error($e->getMessage() ?: __('something_went_wrong_please_try_again'));
            return back()->withInput();
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ]);

        try {
            $res = $this->profileRepo->changePassword($request);
            if ($res) {
                Toastr::success(__('password_changed_successfully'));
            } else {
                Toastr::error(__('current_password_does_not_match'));
            }
            return back();
        } catch (\Throwable $e) {
            Log::error('DeliveryMan Password change error: ' . $e->getMessage());
            Toastr::error($e->getMessage() ?: __('something_went_wrong_please_try_again'));
            return back();
        }
    }
}
