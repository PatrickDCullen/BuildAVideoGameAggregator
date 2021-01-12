<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// had to add this (got it from documentation) so that it knows where the Http
// class is coming from
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class GamesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $before = Carbon::now()->subMonths(2)->timestamp;
        $after = Carbon::now()->addMonths(2)->timestamp;
        $current = Carbon::now()->timestamp;
        $afterFourMonths = Carbon::now()->addMonths(4)->timestamp;
        
        // important note: when adding to services.php and .env file, need to run
        // php artisan config:cache to update what is accessible in the browser
        // I did this after stopping and starting php artisan serve as well
        // (might not have to)
        $mostRatedRecentGames = Http::withHeaders(config('services.igdb'))
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

        $recentlyReviewed = Http::withHeaders(config('services.igdb'))
        ->withBody(
            "fields name, cover.url, first_release_date, total_rating_count, platforms.abbreviation, rating, rating_count, summary;
            where total_rating_count > 1
            & platforms = (48, 49, 130, 6)
            & (first_release_date >= {$before}
            & first_release_date < {$current}
            & rating_count > 5);
            sort total_rating_count desc;
            limit 3;",
            'text/plain'
        )->post('https://api.igdb.com/v4/games')
        ->json();

        $mostAnticipated = Http::withHeaders(config('services.igdb'))
        ->withBody(
            "fields name, cover.url, first_release_date, total_rating_count, platforms.abbreviation, rating, rating_count, summary;
            where platforms = (48, 49, 130, 6)
            & cover != null
            & (first_release_date >= {$current}
            & first_release_date < {$afterFourMonths});
            sort total_rating_count desc;
            limit 4;",
            'text/plain'
        )->post('https://api.igdb.com/v4/games')
        ->json();

        $comingSoon = Http::withHeaders(config('services.igdb'))
        ->withBody(
            "fields name, cover.url, first_release_date, total_rating_count, platforms.abbreviation, rating, rating_count, summary;
            where platforms = (48,49,130,6)
            & cover != null
            & first_release_date >= {$current};
            sort first_release_date asc;
            limit 4;",
            'text/plain'
        )->post('https://api.igdb.com/v4/games')
        ->json();

        return view('index', [
            'mostRatedRecentGames' => $mostRatedRecentGames,
            'recentlyReviewed' => $recentlyReviewed,
            'mostAnticipated' => $mostAnticipated,
            'comingSoon' => $comingSoon,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
