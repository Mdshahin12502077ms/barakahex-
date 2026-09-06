<?php

namespace App\Services;

use Illuminate\Support\ServiceProvider;


class RepositoryServiceProvider extends ServiceProvider{

    public function register(){
        //Role Management
        $this->app->bind(
            'App\Repositories\Interfaces\Role\RoleInterface',
            'App\Repositories\Role\RoleRepository'
        );
        $this->app->bind(
            'App\Repositories\Interfaces\UserInterface',
            'App\Repositories\UserRepository'
        );

        $this->app->bind(
            'App\Repositories\Interfaces\PermissionInterface',
            'App\Repositories\PermissionRepository'
        );

        //Payment method
        $this->app->bind(
            'App\Repositories\Interfaces\Admin\PaymentMethodInterface',
            'App\Repositories\Admin\PaymentMethodRepository'
        );

        //Merchant Management
        $this->app->bind(
            'App\Repositories\Interfaces\Merchant\MerchantInterface',
            'App\Repositories\Merchant\MerchantRepository'
        );

        //delivery man Management
        $this->app->bind(
            'App\Repositories\Interfaces\DeliveryManInterface',
            'App\Repositories\DeliveryManRepository'
        );

        //parcel Management
        $this->app->bind(
            'App\Repositories\Interfaces\ParcelInterface',
            'App\Repositories\ParcelRepository'
        );

        //Account Management
        $this->app->bind(
            'App\Repositories\Interfaces\AccountInterface',
            'App\Repositories\AccountRepository'
        );

        //withdraw Management
        $this->app->bind(
            'App\Repositories\Interfaces\WithdrawInterface',
            'App\Repositories\WithdrawRepository'
        );

        //withdraw Management
        $this->app->bind(
            'App\Repositories\Interfaces\Admin\WithdrawInterface',
            'App\Repositories\Admin\WithdrawRepository'
        );

        //expense Management
        $this->app->bind(
            'App\Repositories\Interfaces\Admin\ExpenseInterface',
            'App\Repositories\Admin\ExpenseRepository'
        );

        //expense Management
        $this->app->bind(
            'App\Repositories\Interfaces\Admin\BankAccountInterface',
            'App\Repositories\Admin\BankAccountRepository'
        );

        //reports Management
        $this->app->bind(
            'App\Repositories\Interfaces\Admin\ReportInterface',
            'App\Repositories\Admin\ReportRepository'
        );

        //reports Management
        $this->app->bind(
            'App\Repositories\Interfaces\Admin\FundTransferInterface',
            'App\Repositories\Admin\FundTransferRepository'
        );

        //bulk Management
        $this->app->bind(
            'App\Repositories\Interfaces\BulkInterface',
            'App\Repositories\BulkRepository'
        );

        //Branch Management
        $this->app->bind(
            'App\Repositories\Interfaces\BranchInterface',
            'App\Repositories\BranchRepository'
        );

        //setting Management
        $this->app->bind(
            'App\Repositories\Interfaces\Admin\SettingInterface',
            'App\Repositories\Admin\SettingRepository'
        );

        //notice Management
        $this->app->bind(
            'App\Repositories\Interfaces\NoticeInterface',
            'App\Repositories\NoticeRepository'
        );

        //thirdParty Management
        $this->app->bind(
            'App\Repositories\Interfaces\Admin\ThirdPartyInterface',
            'App\Repositories\Admin\ThirdPartyRepository'
        );

        //merchant staff Management
        $this->app->bind(
            'App\Repositories\Interfaces\MerchantStaffInterface',
            'App\Repositories\MerchantStaffRepository'
        );

        //merchant staff Management
        $this->app->bind(
            'App\Repositories\Interfaces\Admin\BulkWithdrawInterface',
            'App\Repositories\Admin\BulkWithdrawRepository'
        );

        $this->app->bind(
            'App\Repositories\Interfaces\Admin\PreferenceInterface',
            'App\Repositories\Admin\PreferenceRepository'
        );

        $this->app->bind(
            'App\Repositories\NotificationRepository'
        );
    }
}
