<?php

namespace App\Imports;

use App\Models\Gaji;
use Maatwebsite\Excel\Concerns\ToModel;

class GajiImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Gaji([
            //
        ]);
    }
}
