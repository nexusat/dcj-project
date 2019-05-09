<?php

namespace App\Http\Controllers;

use App\Conflict;
use Illuminate\Http\Request;
use App\Imports\ConflictImport;
use App\Http\Requests\StoreConflict;

class ConflictController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $conflicts = Conflict::withCount('justices')->paginate(50);

        return view('conflict.index', compact('conflicts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('conflict.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreConflict  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Conflict  $conflict
     * @return \Illuminate\Http\Response
     */
    public function show(Conflict $conflict, Request $request)
    {
        $justiceType = $request->query('justice_type') ?? null;

        if ($justiceType) {
            $justices = $conflict->justices()->where('type', $justiceType)
                ->latest('updated_at')
                ->get();
        } else {
            $justices = $conflict->justices()->latest('updated_at')->get();
        }

        return view('conflict.show', compact('conflict', 'justices', 'justiceType'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Conflict  $conflict
     * @return \Illuminate\Http\Response
     */
    public function edit(Conflict $conflict)
    {
        return view('conflict.edit', compact('conflict'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreConflict  $request
     * @param  \App\Conflict  $conflict
     * @return \Illuminate\Http\Response
     */
    public function update(StoreConflict $request, Conflict $conflict)
    {
        $conflict->location = $request->input('location');
        $conflict->territory = $request->input('territory');
        $conflict->side_a = $request->input('side_a');
        $conflict->side_b = $request->input('side_b');

        $conflict->save();

        return redirect()->route('conflict.edit', $conflict);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Conflict  $conflict
     * @return \Illuminate\Http\Response
     */
    public function destroy(Conflict $conflict)
    {
        //
    }
}