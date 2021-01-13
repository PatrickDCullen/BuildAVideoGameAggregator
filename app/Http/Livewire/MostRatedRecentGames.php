<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;


class MostRatedRecentGames extends Component
{

    public $mostRatedRecentGames = [];

    public function loadMostRatedRecentGames()
    {
        $before = Carbon::now()->subMonths(2)->timestamp;
        $after = Carbon::now()->addMonths(2)->timestamp;

        $this->mostRatedRecentGames = Cache::remember('most-rated-recent-games', 7, function () use ($before, $after) {
            return Http::withHeaders(config('services.igdb'))
                ->withBody(
                    "fields name, cover.url, first_release_date, total_rating_count, platforms.abbreviation, rating;
                    where total_rating_count > 1
                    & platforms = (48, 49, 130, 6)
                    & (first_release_date >= {$before}
                    & first_release_date < {$after});
                    sort total_rating_count desc;
                    limit 12;",
                    'text/plain'
                )->post('https://api.igdb.com/v4/games')
                ->json();
        });
        // important note: when adding to services.php and .env file, need to run
        // php artisan config:cache to update what is accessible in the browser
        // I did this after stopping and starting php artisan serve as well
        // (might not have to)

        // $this->mostRatedRecentGames = Http::withHeaders(config('services.igdb'))
        // ->withBody(
        //     "fields name, cover.url, first_release_date, total_rating_count, platforms.abbreviation, rating;
        //     where total_rating_count > 1
        //     & platforms = (48, 49, 130, 6)
        //     & (first_release_date >= {$before}
        //     & first_release_date < {$after});
        //     sort total_rating_count desc;
        //     limit 12;",
        //     'text/plain'
        // )->post('https://api.igdb.com/v4/games')
        // ->json();
    }

    public function render()
    {
        return view('livewire.most-rated-recent-games');
    }
}
