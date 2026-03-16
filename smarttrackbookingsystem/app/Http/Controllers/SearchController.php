<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SearchService;
use App\Models\Business;
class SearchController extends Controller
{
     protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function global(Request $request, Business $business)
    {
        $request->validate([
            'q' => 'required|string|min:2'
        ]);

        $results = $this->searchService->globalSearch($request->q, $business->slug);

        return view('search.results', compact('results'));
    }
    public function ajaxSearch(Request $request, Business $business)
{
    if (!$request->ajax()) {
        abort(404);
    }
    $businessSlug = $business->slug;
    $request->validate([
        'q' => 'required|string|min:1'
    ]);

    $results = $this->searchService->globalSearch($request->q, $businessSlug);

    return response()->json($results);
}
}
