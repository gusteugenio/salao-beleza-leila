<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessHour;

class BusinessHourSeeder extends Seeder
{
  public function run(): void
  {
    // 1 (Segunda) a 6 (Sábado)
    for ($day = 1; $day <= 6; $day++) {
      BusinessHour::create([
        'day_of_week' => $day,
        'open_time' => '09:00:00',
        'close_time' => '18:00:00',
        'lunch_start' => '12:00:00',
        'lunch_end' => '13:00:00',
      ]);
    }
  }
}
