<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentinel\Roles\EloquentRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends EloquentRole
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'permissions'];

    protected $casts = [
        'permissions' => 'array',
        'status' => StatusEnum::class,
    ];

    public function withUsers() {
		return $this -> belongsToMany( static::$usersModel , 'role_users' , 'role_id' , 'user_id' ) -> withTimestamps() ;
	}

    public static function allRole(){
        return Static::all();
    }

    public function scopeWithoutSuperadmin($query)
    {
        return $query->where('id', '!=', 1);
    }

    public function scopeActive($query)
    {
        return $query->where('status', StatusEnum::ACTIVE);
    }

}
