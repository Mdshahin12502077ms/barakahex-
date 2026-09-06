<?php

namespace App\Repositories;
use Image;
use App\Models\User;
use App\Models\RoleUser;
use App\Enums\StatusEnum;
use App\Traits\CommonHelperTrait;
use App\Traits\RepoResponseTrait;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Image as ImageModel;
use App\Traits\ApiReturnFormatTrait;
use App\Repositories\Interfaces\UserInterface;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Laravel\Facades\Activation;

class UserRepository implements UserInterface {
    use RepoResponseTrait, ApiReturnFormatTrait, ImageTrait;

    private $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return User::get();
    }

    public function paginate($limit)
    {
        return User::where('id', '!=', \Sentinel::getUser()->id)->where('id', '!=', '1')->where('user_type', 'staff')->paginate($limit);
    }

    public function get($id)
    {
        return User::with('roleUser')->find($id);
    }



    public function store($request)
    {

        DB::beginTransaction();
        try{


            if (isset($request['image_id'] )) {
                $response                    = $this->saveImage($request['image_id'] ,'image');
                $images                      = $response['images'];
            }


            $user                               = new User();
            $user->first_name                   = $request['first_name'];
            $user->last_name                    = $request['last_name'];
            $user->email                        = $request['email'];
            $user->dashboard                    = $request['dashboard'];
            $user->password                     = bcrypt($request['password']);
            $user->permissions                  = isset($request['permissions']) ? $request['permissions'] : [];
            $user->image_id                     = $images ?? null;
            $user->branch_id                    = $request['branch'] ? $request['branch'] : null;
            $user->save();

            $role                = new RoleUser();
            $role->user_id       = $user->id;
            $role->role_id       = $request['role'];
            $role->save();

            $activation = Activation::create($user);
            Activation::complete($user, $activation->code);
            //$superAdminRole->users()->attach($superAdmin);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($request)
    {
        DB::beginTransaction();
        try{
            $user                            = User::find($request['id']);

            if (isset($request['image_id'] )) {
                $response                    = $this->saveImage($request['image_id'] ,'image');
                $images                      = $response['images'];
                $user->image_id              = $images;
            }

            $user->first_name                   = $request['first_name'];
            $user->last_name                    = $request['last_name'];
            $user->email                        = $request['email'];
            $user->branch_id                    = $request['branch'] ? $request['branch'] : null;
            $user->dashboard                    = $request['dashboard'];

            if($request['password'] != ""):
                $user->password                 = bcrypt($request['password']);
            endif;
            $user->permissions                  = isset($request['permissions']) ? $request['permissions'] : [];
            $user->save();

            $existingRole                       = RoleUser::where('user_id', $user->id)->first();

            if ($existingRole) {
                $existingRole->role_id          = $request['role'];
                $existingRole->save();
            } else {
                RoleUser::create([
                    'user_id' => $user->id,
                    'role_id' => $request['role'],
                ]);
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try{

            $user  = User::find($id);
            $image = ImageModel::find($user->image_id);
            if(!blank($image)):
                if($image->original_image != "" && file_exists($image->original_image)):
                    unlink($image->original_image);
                endif;
                if($image->image_small_one != "" && file_exists($image->image_small_one)):
                    unlink($image->image_small_one);
                endif;
                if($image->image_small_two != "" && file_exists($image->image_small_two)):
                    unlink($image->image_small_two);
                endif;
                if($image->image_small_three != "" && file_exists($image->image_small_three)):
                    unlink($image->image_small_three);
                endif;
                $image->delete();
            else:
                $user->delete();
            endif;

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }


    public function statusChange($request)
    {
        try {
            DB::beginTransaction();
            $row                = $this->model->find($request->id);
            if ($row->status == StatusEnum::ACTIVE) {
                $row->status    = StatusEnum::INACTIVE;
            } elseif ($row->status == StatusEnum::INACTIVE) {
                $row->status    = StatusEnum::ACTIVE;
            }
            $row->save();
            DB::commit();
            return $this->responseWithSuccess('updated successfully', []);
        } catch (\Throwable $th) {
            dd($th->getMessage());
            DB::rollback();
            return $this->responseWithError($th->getMessage(), []);
        }
    }



    public function updateProfile($request)
    {
        DB::beginTransaction();
        try{
            $id    = Sentinel::getUser()->id ?? jwtUser()->id;

            $user  = User::find($id);

            if (isset($request['image_id'] )) {
                $response                    = $this->saveImage($request['image_id'] ,'image');
                $images                      = $response['images'];
                $user->image_id              = $images;
            }

            $user->first_name    = $request['first_name'];
            $user->last_name     = $request['last_name'];
            $user->email         = $request['email'];
            if($user->user_type == 'merchant_staff'):
                $user->phone_number = $request['phone_number'];
            endif;

            $user->save();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
}
