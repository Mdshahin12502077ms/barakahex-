<?php

namespace App\Imports;

use App\Models\Area;
use App\Models\Country;
use App\Models\Division;
use App\Models\District;
use App\Models\Thana;
use App\Models\DeliveryZone;
use App\Enums\StatusEnum;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class AreaImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    private $defaultCountryId;

    public function __construct()
    {
        $defaultCountry = Country::where('name', 'LIKE', '%Bangladesh%')
            ->orWhere('iso2', 'BD')
            ->first() ?? Country::where('status', StatusEnum::ACTIVE)->first() ?? Country::first();

        $this->defaultCountryId = $defaultCountry ? $defaultCountry->id : 19;
    }

    public function model(array $row)
    {
        $areaName     = trim($row['area_name'] ?? $row['name'] ?? $row['area'] ?? '');
        $zoneName     = trim($row['delivery_zone'] ?? $row['zone'] ?? $row['delivery_zone_name'] ?? '');
        $thanaName    = trim($row['thana'] ?? $row['upazila'] ?? $row['thana_name'] ?? '');
        $districtName = trim($row['district'] ?? $row['district_name'] ?? $row['zila'] ?? '');
        $divisionName = trim($row['division'] ?? $row['division_name'] ?? $row['bibhag'] ?? '');
        $countryName  = trim($row['country'] ?? $row['country_name'] ?? '');

        if (empty($areaName) || empty($zoneName)) {
            return null;
        }

        $userId = auth()->id() ?? 1;

    
        $countryId = $this->defaultCountryId;
        if (!empty($countryName)) {
            $country = Country::where('name', 'LIKE', $countryName)->first();
            if ($country) {
                $countryId = $country->id;
            }
        }

       
        $division = null;
        if (!empty($divisionName)) {
            $division = Division::where('name', 'LIKE', $divisionName)->first();
            if (!$division) {
                $division = Division::create([
                    'name'       => $divisionName,
                    'country_id' => $countryId,
                    'status'     => StatusEnum::ACTIVE,
                    'created_by' => $userId,
                ]);
            }
        }

       
        $district = null;
        if (!empty($districtName)) {
            $districtQuery = District::where('name', 'LIKE', $districtName);
            if ($division) {
                $districtQuery->where('division_id', $division->id);
            }
            $district = $districtQuery->first();

            if (!$district) {
                $fallbackDivId = $division ? $division->id : $this->getFallbackDivisionId($countryId);
                $district = District::create([
                    'name'        => $districtName,
                    'division_id' => $fallbackDivId,
                    'status'      => StatusEnum::ACTIVE,
                    'created_by'  => $userId,
                ]);
            }
            if ($district && !$division && $district->division_id) {
                $division = $district->division;
            }
        }

      
        $thana = null;
        if (!empty($thanaName)) {
            $thanaQuery = Thana::where('name', 'LIKE', $thanaName);
            if ($district) {
                $thanaQuery->where('district_id', $district->id);
            }
            $thana = $thanaQuery->first();

            if (!$thana) {
                $fallbackDistId = $district ? $district->id : $this->getFallbackDistrictId($division ? $division->id : null);
                $thana = Thana::create([
                    'name'        => $thanaName,
                    'district_id' => $fallbackDistId,
                    'status'      => StatusEnum::ACTIVE,
                    'created_by'  => $userId,
                ]);
            }
            if ($thana && !$district && $thana->district_id) {
                $district = $thana->district;
                $division = $district ? $district->division : $division;
            }
        }

     
        $zoneQuery = DeliveryZone::where('name', 'LIKE', $zoneName);
        if ($thana) {
            $zoneQuery->where('thana_id', $thana->id);
        }
        $zone = $zoneQuery->first();

        if (!$zone) {
            $fallbackThanaId = $thana ? $thana->id : $this->getFallbackThanaId($district ? $district->id : null);
            $zone = DeliveryZone::create([
                'name'       => $zoneName,
                'thana_id'   => $fallbackThanaId,
                'status'     => StatusEnum::ACTIVE,
                'created_by' => $userId,
            ]);
        }

      
        $finalThanaId    = $zone->thana_id ?? ($thana->id ?? null);
        $finalDistrictId = $district->id ?? ($zone->thana->district_id ?? null);
        $finalDivisionId = $division->id ?? ($zone->thana->district->division_id ?? null);
        $finalCountryId  = $countryId ?? ($zone->thana->district->division->country_id ?? $this->defaultCountryId);

        
        return Area::updateOrCreate(
            [
                'name'             => $areaName,
                'delivery_zone_id' => $zone->id,
            ],
            [
                'thana_id'    => $finalThanaId,
                'district_id' => $finalDistrictId,
                'division_id' => $finalDivisionId,
                'country_id'  => $finalCountryId,
                'status'      => StatusEnum::ACTIVE,
                'created_by'  => $userId,
            ]
        );
    }

    private function getFallbackDivisionId($countryId)
    {
        $div = Division::where('country_id', $countryId)->first() ?? Division::first();
        if ($div) {
            return $div->id;
        }
        $div = Division::create(['name' => 'Default Division', 'country_id' => $countryId, 'status' => StatusEnum::ACTIVE]);
        return $div->id;
    }

    private function getFallbackDistrictId($divisionId)
    {
        $dist = District::first();
        if ($dist) {
            return $dist->id;
        }
        $divId = $divisionId ?? $this->getFallbackDivisionId($this->defaultCountryId);
        $dist = District::create(['name' => 'Default District', 'division_id' => $divId, 'status' => StatusEnum::ACTIVE]);
        return $dist->id;
    }

    private function getFallbackThanaId($districtId)
    {
        $thana = Thana::first();
        if ($thana) {
            return $thana->id;
        }
        $distId = $districtId ?? $this->getFallbackDistrictId(null);
        $thana = Thana::create(['name' => 'Default Thana', 'district_id' => $distId, 'status' => StatusEnum::ACTIVE]);
        return $thana->id;
    }
}
