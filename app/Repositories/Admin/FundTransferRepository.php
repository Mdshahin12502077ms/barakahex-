<?php

namespace App\Repositories\Admin;

use App\Repositories\Interfaces\Admin\FundTransferInterface;
use App\Models\Account\MerchantWithdraw;
use App\Models\Account\CompanyAccount;
use App\Models\Account\MerchantAccount;
use App\Models\Account\FundTransfer;
use App\Models\StaffAccount;
use App\Models\Merchant;
use DB;
use Image;
use App\Traits\CommonHelperTrait;

class FundTransferRepository implements FundTransferInterface {

    use CommonHelperTrait;

    public function all()
    {
        return FundTransfer::orderBy('id', 'desc')->paginate(\Config::get('parcel.paginate'));
    }
    public function get($id)
    {
        return FundTransfer::find($id);
    }
    public function store($request)
    {
        DB::beginTransaction();
        try{

            $transfer                          = new FundTransfer();
            $transfer->from_account_id         = $request->from_account;
            $transfer->to_account_id           = $request->to_account;
            $transfer->amount                  = $request->amount;
            $transfer->note                    = $request->note;
            $transfer->date                    = date('Y-m-d', strtotime($request->date));
            $transfer->save();

            $staff_account                     = new StaffAccount();
            $staff_account->details            = 'fund_transfered_to';
            $staff_account->source             = 'fund_transfer';
            $staff_account->date               = date('Y-m-d', strtotime($request->date));
            $staff_account->type               = 'expense';
            $staff_account->amount             = $request->amount;
            $staff_account->fund_transfer_id   = $transfer->id;
            $staff_account->from_account_id       = $request->from_account;
            $staff_account->save();

            // to account transaction
            $staff_account                     = new StaffAccount();
            $staff_account->details            = 'fund_received_from';
            $staff_account->source             = 'fund_receive';
            $staff_account->date               = date('Y-m-d', strtotime($request->date));
            $staff_account->type               = 'income';
            $staff_account->amount             = $request->amount;
            $staff_account->fund_transfer_id   = $transfer->id;
            $staff_account->to_account_id      = $request->to_account;
            $staff_account->save();
            // new account system

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

            $transfer                          = FundTransfer::find($request->id);
            $transfer->from_account_id         = $request->from_account;
            $transfer->to_account_id           = $request->to_account;
            $transfer->amount                  = $request->amount;
            $transfer->note                    = $request->note;
            $transfer->date                    = date('Y-m-d', strtotime($request->date));
            $transfer->save();

            $staff_account                     = StaffAccount::where('fund_transfer_id', $transfer->id)->where('type', 'expense')->first();
            $staff_account->details            = 'fund_transfered_to';
            $staff_account->source             = 'fund_transfer';
            $staff_account->date               = date('Y-m-d', strtotime($request->date));
            $staff_account->type               = 'expense';
            $staff_account->amount             = $request->amount;
            $staff_account->from_account_id    = $request->from_account;
            $staff_account->save();

            // to account transaction
            $staff_account                     = StaffAccount::where('fund_transfer_id', $transfer->id)->where('type', 'income')->first();
            $staff_account->details            = 'fund_received_from';
            $staff_account->source             = 'fund_receive';
            $staff_account->date               = date('Y-m-d', strtotime($request->date));
            $staff_account->type               = 'income';
            $staff_account->amount             = $request->amount;
            $staff_account->to_account_id      = $request->to_account;
            $staff_account->save();
            // new account system

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

            FundTransfer::destroy($id);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function fileUpload($image){

        $requestImage           = $image;
        $fileType               = $requestImage->getClientOriginalExtension();

        $original   = date('YmdHis') .'-receipt' . rand(1, 50) . '.' . $fileType;
        $directory              = 'admin/images/';

        if(!is_dir($directory)) {
            mkdir($directory);
        }

        $originalFileUrl       = $directory . $original;

        if($fileType == 'pdf'):
            $requestImage->move($directory,$original);
        else:
            Image::make($requestImage)->save($originalFileUrl, 80);
        endif;

        return $originalFileUrl;
    }

    public function removeOldFile($image)
    {
        if($image != "" && file_exists($image)):
            unlink($image);
        endif;
    }
}
