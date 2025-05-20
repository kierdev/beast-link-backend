<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Applicant;

class ApplicantSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'Ken Singson',
                'email' => 'kt.singson06@gmail.com',
                'course' => 'BSIT',
                'status' => 'PASSED',
            ],
            [
                'name' => 'Jane Cooper',
                'email' => 'janecoop@gmail.com',
                'course' => 'BSIT',
                'status' => 'FAILED',
            ],
            [
                'name' => 'Annette Black',
                'email' => 'annette2@gmail.com',
                'course' => 'BSIT',
                'status' => 'FAILED',
            ],
            [
                'name' => 'Jane Cooper',
                'email' => 'janecoop2@gmail.com',
                'course' => 'BSIT',
                'status' => 'PASSED',
            ],
        ];

        foreach ($data as $item) {
            Applicant::create($item);
        }
    }
}
