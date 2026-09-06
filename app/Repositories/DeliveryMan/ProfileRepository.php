<?php

namespace App\Repositories\DeliveryMan;

use App\Models\User;
use App\Models\DeliveryMan;
use App\Traits\RepoResponseTrait;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\ImageTrait;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProfileRepository
{
    use RepoResponseTrait, ApiReturnFormatTrait, ImageTrait;

    protected $model;

    public function __construct(DeliveryMan $model)
    {
        $this->model = $model;
    }

    public function getProfile()
    {
        $user = Sentinel::getUser();
        if (!$user) {
            return null;
        }
        return $user->deliveryMan ?? DeliveryMan::where('user_id', $user->id)->first();
    }

    public function updateProfile($request)
    {
        DB::beginTransaction();
        try {
            $authUser = Sentinel::getUser();
            if (!$authUser) {
                throw new \Exception(__('user_not_found'));
            }

            $user = User::findOrFail($authUser->id);
            $deliveryMan = $user->deliveryMan ?? DeliveryMan::where('user_id', $user->id)->first();

            // Update User fields
            $user->first_name   = $request->first_name ?? $user->first_name;
            $user->last_name    = $request->last_name ?? $user->last_name;
            $user->email        = $request->email ?? $user->email;
            $user->phone_number = $request->phone_number ?? $user->phone_number;

            if ($request->hasFile('image') || $request->hasFile('image_id')) {
                $file = $request->file('image') ?: $request->file('image_id');
                $response = $this->saveImage($file, 'image');
                if (is_array($response) && isset($response['images'])) {
                    $user->image_id = $response['images'];
                }
            }
            $user->save();

            // Sync with current session
            if ($authUser) {
                $authUser->image_id = $user->image_id;
                $authUser->first_name = $user->first_name;
                $authUser->last_name = $user->last_name;
                $authUser->email = $user->email;
                $authUser->phone_number = $user->phone_number;
            }

            // Update DeliveryMan fields
            if (!$deliveryMan) {
                $deliveryMan = new DeliveryMan();
                $deliveryMan->user_id = $user->id;
            }

            $deliveryMan->phone_number = $request->phone_number ?? ($deliveryMan->phone_number ?? $user->phone_number);
            $deliveryMan->address      = $request->address ?? $deliveryMan->address;
            $deliveryMan->city         = $request->city ?? $deliveryMan->city;
            $deliveryMan->zip          = $request->zip ?? $deliveryMan->zip;

            if ($request->hasFile('driving_license')) {
                $file = $request->file('driving_license');
                $ext = strtolower($file->getClientOriginalExtension());
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $response = $this->saveImage($file, 'driving_license');
                    if (is_array($response) && isset($response['images'])) {
                        $deliveryMan->driving_license = $response['images'];
                    }
                } else {
                    File::ensureDirectoryExists(public_path('images/'), 0777, true);
                    $fileName = date('YmdHis') . '_license_' . rand(1, 500) . '.' . $ext;
                    $file->move(public_path('images/'), $fileName);
                    $deliveryMan->driving_license = [
                        'storage'        => 'local',
                        'original_image' => 'images/' . $fileName,
                    ];
                }
            }
            $deliveryMan->save();

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function changePassword($request)
    {
        DB::beginTransaction();
        try {
            $user = Sentinel::getUser();
            $hasher = Sentinel::getHasher();

            if (!$hasher->check($request->current_password, $user->password)) {
                return false;
            }

            Sentinel::update($user, ['password' => $request->password]);

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }
}
