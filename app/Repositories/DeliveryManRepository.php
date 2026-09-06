<?php

namespace App\Repositories;
use Image;
use App\Models\User;
use App\Enums\StatusEnum;
use App\Models\DeliveryMan;
use App\Traits\CommonHelperTrait;
use App\Traits\RepoResponseTrait;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Image as ImageModel;
use App\Traits\ApiReturnFormatTrait;
use App\Models\Account\CompanyAccount;
use App\Models\Account\DeliveryManAccount;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use App\Repositories\Interfaces\DeliveryManInterface;


class DeliveryManRepository implements DeliveryManInterface
{

    use RepoResponseTrait, ApiReturnFormatTrait, ImageTrait;

    private $model;

    public function __construct(DeliveryMan $model)
    {
        $this->model = $model;
    }


    public function all()
    {
        return DeliveryMan::all();
    }

    public function activeAll()
    {
        return DeliveryMan::whereHas('user', function ($query) {
            $query->where('status', 1);
        })
            ->when(!hasPermission('read_all_delivery_man'), function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('branch_id', Sentinel::getUser()->branch_id)
                        ->orWhere('branch_id', null);
                });
            })->get();
    }

    public function paginate($limit)
    {
        return DeliveryMan::with('user')
            ->when(!hasPermission('read_all_delivery_man'), function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('branch_id', Sentinel::getUser()->branch_id)
                        ->orWhere('branch_id', null);
                });
            })->orderBy('id', 'desc')->paginate($limit);
    }

    public function get($id)
    {
        return DeliveryMan::find($id);
    }

    public function save($role, $data)
    {

    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            if (isset($request['image_id'])) {
                $response = $this->saveImage($request['image_id'], 'image');
                $images = $response['images'];
            }

            if (isset($request['driving_license'])) {
                $response = $this->saveImage($request['driving_license'], 'image');
                $driving_license = $response['images'];
            }

            $user = new User();
            $user->first_name = $request['first_name'];
            $user->last_name = $request['last_name'];
            $user->email = $request['email'];
            $user->phone_number = $request['phone_number'];
            $user->branch_id = $request['branch'] ? $request['branch'] : 1;
            $user->password = bcrypt($request['password']);
            $user->permissions = \Config::get('parcel.delivery_man_permissions');
            $user->image_id = $images ?? null;
            $user->user_type = 'delivery';
            $user->save();

            $deliveryman = new DeliveryMan();
            $deliveryman->user_id = $user->id;
            $deliveryman->phone_number = $request['phone_number'];
            $deliveryman->city = $request['city'];
            $deliveryman->zip = $request['zip'];
            $deliveryman->address = $request['address'];
            $deliveryman->delivery_fee = $request['delivery_fee'];
            $deliveryman->pick_up_fee = $request['pick_up_fee'];
            $deliveryman->return_fee = $request['return_fee'];
            $deliveryman->driving_license = $driving_license ?? null;
            $deliveryman->save();

            $activation = Activation::create($user);
            Activation::complete($user, $activation->code);


            $company_account = new CompanyAccount();
            $company_account->details = 'delivery_man_opening_balance';
            $company_account->source = 'opening_balance';
            $company_account->date = date('Y-m-d');
            $company_account->type = 'income';
            $company_account->amount = $request['opening_balance'];
            $company_account->created_by = Sentinel::getUser()->id;
            $company_account->delivery_man_id = $deliveryman->id;
            $company_account->save();

            $deliveryman_account = new DeliveryManAccount();
            $deliveryman_account->details = 'delivery_man_opening_balance';
            $deliveryman_account->source = 'opening_balance';
            $deliveryman_account->date = date('Y-m-d');
            $deliveryman_account->type = 'income';
            $deliveryman_account->amount = $request['opening_balance'];
            $deliveryman_account->delivery_man_id = $deliveryman->id;
            $deliveryman_account->company_account_id = $company_account->id;
            $deliveryman_account->save();
            // new account

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
        try {

            $user = User::find($request['user_id']);

            if (isset($request['image_id'])) {
                $response = $this->saveImage($request['image_id'], 'image');
                $images = $response['images'];
            }

            if (isset($request['driving_license'])) {
                $response = $this->saveImage($request['driving_license'], 'image');
                $driving_license = $response['images'];
            }


            $user->first_name = $request['first_name'];
            $user->last_name = $request['last_name'];
            $user->email = $request['email'];
            $user->branch_id = $request['branch'] ? $request['branch'] : 1;
            $user->phone_number = $request['phone_number'];
            $user->image_id = $images ?? $user->image_id;

            if ($request['password'] != ""):
                $user->password = bcrypt($request['password']);
            endif;
            $user->save();

            $deliveryman = DeliveryMan::find($request['id']);
            $deliveryman->user_id = $user->id;
            $deliveryman->phone_number = $request['phone_number'];
            $deliveryman->city = $request['city'];
            $deliveryman->zip = $request['zip'];
            $deliveryman->address = $request['address'];
            $deliveryman->delivery_fee = $request['delivery_fee'];
            $deliveryman->pick_up_fee = $request['pick_up_fee'];
            $deliveryman->return_fee = $request['return_fee'];
            $deliveryman->driving_license = $driving_license ?? $deliveryman->driving_license;
            $deliveryman->save();

            // new account

            $company_account = CompanyAccount::where('source', 'opening_balance')->where('delivery_man_id', $deliveryman->id)->first();
            $company_account->details = 'delivery_man_opening_balance';
            $company_account->source = 'opening_balance';
            $company_account->date = date('Y-m-d');
            $company_account->type = 'income';
            $company_account->amount = $request['opening_balance'];
            $company_account->created_by = Sentinel::getUser()->id;
            $company_account->delivery_man_id = $deliveryman->id;
            $company_account->save();

            $deliveryman_account = DeliveryManAccount::where('source', 'opening_balance')->where('delivery_man_id', $deliveryman->id)->first();
            $deliveryman_account->details = 'delivery_man_opening_balance';
            $deliveryman_account->source = 'opening_balance';
            $deliveryman_account->date = date('Y-m-d');
            $deliveryman_account->type = 'income';
            $deliveryman_account->amount = $request['opening_balance'];
            $deliveryman_account->delivery_man_id = $deliveryman->id;
            $deliveryman_account->company_account_id = $company_account->id;
            $deliveryman_account->save();
            // new account

            DB::commit();
            return true;

        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            return false;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {

            $delivery_man = DeliveryMan::find($id);


            $user = User::find($delivery_man->user_id);


            CompanyAccount::where('source', 'opening_balance')->where('delivery_man_id', $delivery_man->id)->delete();
            DeliveryManAccount::where('source', 'opening_balance')->where('delivery_man_id', $delivery_man->id)->delete();
            $user->delete();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function imageUpload($image, $type, $delivery_man_id = '')
    {
        if ($delivery_man_id != ''):
            $delivery = DeliveryMan::find($delivery_man_id);
            if ($delivery->driving_license != "" && file_exists($delivery->driving_license)):
                unlink($delivery->driving_license);
            endif;
        endif;

        $requestImage = $image;
        $fileType = $requestImage->getClientOriginalExtension();
        $originalImage = date('YmdHis') . '-' . $type . rand(1, 50) . '.' . $fileType;
        $directory = 'admin/' . $type . '/';

        $storagePath = public_path($directory);
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }
        $originalImageUrl = $storagePath . $originalImage;
        Image::make($requestImage)->save($originalImageUrl, 80);
        return static_asset($directory . $originalImage);
    }

    public function statusChange($request)
    {
        DB::beginTransaction();
        try {

            $row = $this->model->where('user_id', $request->id)->first();
            if ($row->status == StatusEnum::ACTIVE) {

                $row->status = StatusEnum::INACTIVE;
            } elseif ($row->status == StatusEnum::INACTIVE) {
                $row->status = StatusEnum::ACTIVE;
            }
            $row->save();

            $user = User::find($row->user_id);
            if ($user->status == StatusEnum::ACTIVE) {
                $user->status = StatusEnum::INACTIVE;
            } elseif ($user->status == StatusEnum::INACTIVE) {
                $user->status = StatusEnum::ACTIVE;
            }
            $user->save();
            DB::commit();
            return $this->responseWithSuccess(__('updated_successfully'), []);
        } catch (\Throwable $th) {
            dd();
            DB::rollback();
            return $this->responseWithError($th->getMessage(), []);
        }
    }



    public function filter($request)
    {
        $query = DeliveryMan::query();

        if (!hasPermission('read_all_delivery_man')) {
            $query->whereHas('user', function ($q) {
                $q->where('branch_id', Sentinel::getUser()->branch_id)
                    ->orWhere('branch_id', null);
            });
        }

        if ($request->branch != 'all') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->when($request->branch == 'pending', function ($search) {
                    $search->where('branch_id', null);
                })->when($request->branch != 'pending', function ($search) use ($request) {
                    $search->where('branch_id', $request->branch);
                });
            });
        }

        if ($request->name != "") {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->name . '%')
                    ->orWhere('last_name', 'like', '%' . $request->name . '%');
            });
        }

        if ($request->email != "") {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('email', 'like', '%' . $request->email . '%');
            });
        }

        if ($request->status != "any") {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        return $query->orderByDesc('id')->paginate(\Config::get('parcel.paginate'));

    }

}
