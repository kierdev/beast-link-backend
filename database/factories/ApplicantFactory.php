<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicantFactory extends Factory
{
    protected $model = \App\Models\Applicant::class;

    public function definition()
    {
        $startYear = date('Y'); // or use now()->year if Carbon is available
        $startYear = $this->faker->numberBetween(2002, $startYear);
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'academic_year' => $startYear . '-' . ($startYear + 1),
            'course1' => $this->faker->randomElement(['BSIT', 'BSCS', 'BSBA', 'BSHRM', 'BSED', 'BSCE', 'BSEE']),
            'course2' => $this->faker->randomElement(['BSIT', 'BSCS', 'BSBA', 'BSHRM', 'BSED', 'BSCE', 'BSEE']),
        ];
    }
}