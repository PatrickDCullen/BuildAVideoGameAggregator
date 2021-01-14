<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Str;


class MostRatedRecentGames extends Component
{
    public $mostRatedRecentGames = [];

    public function loadMostRatedRecentGames()
    {
        $before = Carbon::now()->subMonths(2)->timestamp;
        $after = Carbon::now()->addMonths(2)->timestamp;

        $mostRatedRecentGamesUnformatted = Cache::remember('most-rated-recent-games', 7, function () use ($before, $after) {
            return Http::withHeaders(config('services.igdb'))
                ->withBody(
                    "fields name, cover.url, first_release_date, total_rating_count, platforms.abbreviation, rating, slug;
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

        // dd($this->formatForView($mostRatedRecentGamesUnformatted));

        $this->mostRatedRecentGames = $this->formatForView($mostRatedRecentGamesUnformatted);
        
    }

    public function render()
    {
        return view('livewire.most-rated-recent-games');
    }

    private function formatForView($games) 
    {
        return collect($games)->map(function ($game) {
            return collect($game)->merge([
                'coverImageUrl' => Str::replaceFirst('thumb', 'cover_big', $game['cover']['url']),
                'rating' => isset($game['rating']) ? round($game['rating']).'%' : null,
                'platforms' => collect($game['platforms'])->pluck('abbreviation')->implode(', '),
            ]);
        })->toArray();
    }
}
