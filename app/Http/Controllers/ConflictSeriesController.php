<?php

namespace App\Http\Controllers;

use Auth;
use App\Conflict;
use App\ConflictSeries;
use Illuminate\Http\Request;

class ConflictSeriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $conflictSeries = ConflictSeries::with('episodes', 'justices');

        $query = $request->query('query');

        $conflictEpisodes = Conflict::where('side_a', 'like', "%$query%")
            ->orWhere('side_b', 'like', "%$query%")
            ->orWhere('territory', 'like', "%$query%")
            ->orWhere('location', 'like', "%$query%")
            ->orWhere('conflict_id', $query)
            ->get();

        $conflictIds = $conflictEpisodes->unique('conflict_id')->pluck('conflict_id');

        $conflictSeries = $conflictSeries->whereIn('id', $conflictIds)->paginate(20); 

        return view('conflict-series.index', compact('conflictSeries', 'query'));
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
     * @param  \App\ConflictSeries  $conflictSeries
     * @return \Illuminate\Http\Response
     */
    public function show(ConflictSeries $conflictSeries, Request $request)
    {
        $taskWorkflow = $request->query('task') ?? false;

        if ($taskWorkflow && !Auth::check()) {
            return redirect()->route('login');
        }

       // $conflictSeries->withCount(['justices']);
        $conflictYears = $conflictSeries->episodes()->with('dyads')->get();

        return view('conflict-series.show', compact('conflictSeries', 'conflictYears', 'taskWorkflow'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ConflictSeries  $conflictSeries
     * @return \Illuminate\Http\Response
     */
    public function edit(ConflictSeries $conflictSeries)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ConflictSeries  $conflictSeries
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ConflictSeries $conflictSeries)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ConflictSeries  $conflictSeries
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConflictSeries $conflictSeries)
    {
        //
    }
}
