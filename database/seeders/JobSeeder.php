<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Job;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $no_of_data = 50000;
        $test_data = array();
        $faker = Faker::create();
        for ($i = 0; $i < $no_of_data; $i++){
            $test_data[$i]['name']                      = $faker->name(5);
            $test_data[$i]['status']                    = 'Not Started';
            $test_data[$i]['site_id']                   = $this->generateRandomString(7);
            $test_data[$i]['platform']                  = 'Wordpress';
            $test_data[$i]['client_id']                 = 2;
            $test_data[$i]['developer_id']              = 1;
            $test_data[$i]['request_type_id']           = 18;
            $test_data[$i]['request_volume_id']         = 2;
            $test_data[$i]['request_sla_id']            = 1;
            $test_data[$i]['salesforce_link']           = $this->generateRandomString(30);
            $test_data[$i]['special_request']           = 0;
            $test_data[$i]['comments_special_request']  = $this->generateRandomString(30);
            $test_data[$i]['addon_comments']            = $this->generateRandomString(30);
            $test_data[$i]['start_at']                  = Carbon::now();
            $test_data[$i]['created_at']                = Carbon::now();
            $test_data[$i]['updated_at']                = Carbon::now();
            $test_data[$i]['created_by']                = 1;
        }

        $chunk_data = array_chunk($test_data, 1000);
        if (isset($chunk_data) && !empty($chunk_data)) {
            foreach ($chunk_data as $chunk_data_val) {
                DB::table('tasks')->insert($chunk_data_val);
            }
        }
    }

    // Generate a random string with the specified length
    function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        $charactersLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
