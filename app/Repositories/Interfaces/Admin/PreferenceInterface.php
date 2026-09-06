<?php

namespace App\Repositories\Interfaces\Admin;

interface PreferenceInterface {

    public function update($request);

    public function statusChange($request);
}
