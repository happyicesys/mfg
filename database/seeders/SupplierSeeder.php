<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    // 1 = MY
    // 2 = SG
    // 3 = US
    // 4 = CN

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Supplier::create([
            'company_name' => 'SMAD ELECTRIC APPLIANCES CO.LTD',
            'attn_name' => 'Avril',
            'attn_contact' => null,
            'email' => 'sales16@smad.com.cn',
            'url' => null,
            'payment_term_id' => null,
            'country_id' => 4,
        ]);

        Supplier::create([
            'company_name' => 'TB-嘉达塑胶绝缘材料制品',
            'attn_name' => null,
            'attn_contact' => null,
            'email' => null,
            'url' => 'https://item.taobao.com/item.htm?spm=a1z09.2.0.0.531f2e8dpIDK5h&id=534346788478&_u=316r4gh4b6c',
            'payment_term_id' => null,
            'country_id' => 4,
        ]);

        Supplier::create([
            'company_name' => 'Allsteelworks L&N Sdn Bhd',
            'attn_name' => 'Alex',
            'attn_contact' => '60137822387',
            'email' => 'allsteelworks@lnengrg.com',
            'url' => null,
            'payment_term_id' => null,
            'country_id' => 1,
        ]);

        Supplier::create([
            'company_name' => '瑞安海坦',
            'attn_name' => null,
            'attn_contact' => null,
            'email' => null,
            'url' => null,
            'payment_term_id' => null,
            'country_id' => 4,
        ]);
    }
}
