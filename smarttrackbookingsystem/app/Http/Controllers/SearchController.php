<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SearchService;
class SearchController extends Controller
{
     protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function global(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2'
        ]);

        $results = $this->searchService->globalSearch($request->q);

        return view('search.results', compact('results'));
    }
    public function ajaxSearch(Request $request)
{
    if (!$request->ajax()) {
        abort(404);
    }

    $request->validate([
        'q' => 'required|string|min:1'
    ]);

    $results = $this->searchService->globalSearch($request->q);

    return response()->json($results);
}
}
