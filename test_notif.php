<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

class TestNotification { 
    use App\Traits\SendNotification; 
    public function test() { 
        $users = App\Models\User::where('user_type', 'staff')->get(); 
        
        // Mock Sentinel to return a user
        Cartalyst\Sentinel\Laravel\Facades\Sentinel::shouldReceive('getUser')->andReturn((object)['id' => 1]);
        
        $this->sendNotification('Test', $users, 'Test Details', ['parcel_read'], 'success', '', '');
    } 
} 
(new TestNotification())->test();
