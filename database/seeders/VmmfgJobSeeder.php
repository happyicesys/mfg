<?php

namespace Database\Seeders;

use App\Models\VmmfgJob;
use App\Models\VmmfgUnit;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VmmfgJobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $job = VmmfgJob::create([
            'batch_no' => 8,
            'model' => 'Salmon SE2',
            'order_date' => Carbon::now()->toDateString(),
        ]);

        VmmfgJob::create([
            'batch_no' => 9,
            'model' => 'Unilever UE3',
            'order_date' => Carbon::now()->toDateString(),
        ]);

        VmmfgUnit::create([
            'unit_no' => 501,
            'vmmfg_job_id' => $job->id,
            'vmmfg_scope_id' => 1,
        ]);

        VmmfgUnit::create([
            'unit_no' => 502,
            'vmmfg_job_id' => $job->id,
            'vmmfg_scope_id' => 1,
        ]);
    }
}
