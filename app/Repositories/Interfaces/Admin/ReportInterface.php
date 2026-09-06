<?php

namespace App\Repositories\Interfaces\Admin;

interface ReportInterface{
    public function parcelSearch($request);

    public function eventQuery($start_date, $end_date, $merchant, $status);

    public function totalSummerySearch($request);

    public function profits($request);
}
