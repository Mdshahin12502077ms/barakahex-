<?php

namespace App\Repositories\Role;

use App\Models\Role;
use App\Enums\StatusEnum;
use App\Traits\RepoResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\Role\RoleInterface;

class RoleRepository implements RoleInterface
{

    use RepoResponseTrait;

    public function all()
    {
        return Role::get();
    }

    public function paginate($limit)
    {
        return Role::paginate($limit);
    }

    public function get($id)
    {
        return Role::findOrFail($id);
    }

    public function store(array $data)
    {
        $role                 = new Role();
        return $this->save($role, $data);
    }

    public function update($id, array $data)
    {
        $role                 = $this->get($id);
        return $this->save($role, $data);
    }

    public function delete($id)
    {
        $role = Role::find($id);
        return $role->delete();
    }

    public function save($role, $data)
    {
        // for new add and update
        $role->name           = $data['name'];
        if ($data['slug'] != null) :
            $role->slug       = $data['slug'];
        else :
            $role->slug       = \Str::slug($data['name'], '-');
        endif;
        $role->permissions    = $data['permissions'] ?? [];
        return $role->save();
    }

    public function statusChange($request)
    {
        try {
            $row = Role::find($request->id);
            if ($row->status == StatusEnum::ACTIVE) {
                $row->status = StatusEnum::INACTIVE;
            } elseif ($row->status == StatusEnum::INACTIVE) {
                $row->status = StatusEnum::ACTIVE;
            }
            $row->save();
            return $this->formatResponse(true, __('updated_successfully'), 'users', []);
        } catch (\Exception $e) {

            dd($e->getMessage());
            DB::rollback();
            return $this->formatResponse(false, __('error'), 'users', []);
        }

    }
}
