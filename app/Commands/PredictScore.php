<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Support\Facades\Log;

use function Termwind\{render};

class PredictScore extends Command
{

    public const URL = 'https://copa22.medeiro.tech/matches/today';
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

        render(<<<'HTML'
        <div class="py-1 ml-2">
            <div class="px-1 bg-blue-300 text-black">Score Predictor Started</div>
            <em class="ml-1">
                Lets get those scores right!
            </em>
        </div>
    HTML);

        //liveFixtures
        $pLiveFixtures = collect(Http::get(self::P_URL)->json()['liveFixtures']);

        $wUpCommingMatches = collect(Http::get(self::URL)->json());

        if ($pLiveFixtures->count() <= 0) {
            $this->info('No live matches found');
            Log::info('No live matches found');
            return;
        }


        $pLiveFixtures->each(function ($match) use ($pLiveFixtures, $wUpCommingMatches) {
            $predicted = false;


            $prediction_closes_at = Carbon::parse($match['prediction_closes_at'])->timezone('Indian/Maldives');


            $now = now();


            $start = Carbon::createFromTimeString($prediction_closes_at->format('H:i'))->subMinutes(2);
            $end = Carbon::createFromTimeString($prediction_closes_at->format('H:i'));

            /*
            if ($start > $end) {
                $end = $end->addDay();
            }
            if ($now->between($start, $end) || $now->addDay()->between($start, $end)) {
*/

                $live_current = $wUpCommingMatches->where('status', 'in_progress')->first();

                $predicted = true;
                $response = $this->predict($match, $live_current['awayTeam']['goals'],  $live_current['homeTeam']['goals']);

                if ($response->failed()) {
                    $this->error($response->json()['errorMessage']);
                    $this->sendToTelegram($response->json()['errorMessage']. ' - ' . $match['home_team']['name'] . ' vs ' . $match['away_team']['name']);
                    Log::error($response->json()['errorMessage']);
                } else {
                    Http::get('https://hc-ping.com/c88e7643-5d37-4891-b905-a1668bdffde8');
                    Log::info('Prediction made for ' . $match['home_team']['name'] . ' vs ' . $match['away_team']['name']);
                    $this->sendToTelegram('Prediction made for ' . $match['home_team']['name'] . ' vs ' . $match['away_team']['name']);
                }
           // } else {
               // $this->info('Prediction time not started. ' . $match['home_team']['name'] . ' vs ' . $match['away_team']['name']);
             //   Log::info('Prediction time not started. ' . $match['home_team']['name'] . ' vs ' . $match['away_team']['name']);
           // }
        });
    }


    public function predict($fixture, $away_team_score, $home_team_score)
    {
        $this->info('Predicting score for ' . $fixture['home_team']['name'] . ' vs ' . $fixture['away_team']['name']);

        return Http::get('https://foariapp.com/api/prediction', [
            'email' => 'j@live.mv',
            'fixture_id' => $fixture['id'],
            'away_team_score' => $away_team_score,
            'home_team_score' => $home_team_score,
        ]);
    }

    public function sendToTelegram($message)
    {

        $apiToken = "5770485049:AAHvNbi6btPxy5Cq4nQOXVg5c9RGmaXdUPY";
        $data = [
            'chat_id' => '-1001899694587',
            'text' => $message
        ];
        file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data));
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class)->everyTwoMinutes()->pingOnSuccess('https://hc-ping.com/904c5800-4229-45cb-810a-9fdb6c9d95b7');
    }
}
