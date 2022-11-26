<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Http;
use LaravelZero\Framework\Commands\Command;

class PredictScore extends Command
{
    public const URL = 'http://api.cup2022.ir/api/v1/match';
    public const P_URL = 'https://foariapp.com/api/fixtures?tournament_uuid=97c1c316-c945-4f28-a4bc-d087e6e35f2d&email=j@live.mv';

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'predict:score';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run the Score predictor';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
                //liveFixtures
        $pLiveFixtures = collect(Http::get(self::P_URL)->json()['fixtures']);


        $wUpCommingMatches = collect(Http::withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJfaWQiOiI2MzgxNGI0ZmY5YzMyYjNmNjMxYjQ3YjUiLCJpYXQiOjE2Njk0MTc4MDgsImV4cCI6MTY2OTUwNDIwOH0.P6-nVYs4dYx4WdgO91xjmbBejtb4KlYpN_MDsFW-13o',
        ])->get(self::URL)->json()['data'])->where('time_elapsed', 'notstarted');




    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
