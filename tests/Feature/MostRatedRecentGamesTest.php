<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use App\Http\Livewire\MostRatedRecentGames;

class MostRatedRecentGamesTest extends TestCase
{
    /** @test */
    public function the_main_page_shows_most_rated_recent_games()
    {
        // Http::fake([
        //     'https://api.igdb.com/v4/games' => $this->fakeMostRatedRecentGames(),
        // ]);

        Livewire::test(MostRatedRecentGames::class)
            ->assertSet('mostRatedRecentGames', [])
            ->call('loadMostRatedRecentGames');
    }

    public function fakeMostRatedRecentGames() 
    {
        // Not sure how to format the data from the request to make it a PHP array
    }

}
