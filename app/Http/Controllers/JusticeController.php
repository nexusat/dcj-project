<?php

namespace App\Http\Controllers;

use App\Conflict;
use App\Http\Requests\JusticeRequest;
use App\Justice;
use Auth;
use Illuminate\Http\Request;

class JusticeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Justice::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $justices = Justice::all();

        return view('justice.index', compact('justices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Conflict $conflict, Request $request)
    {
        $justiceType = $request->input('justice_type') ?? null;
        $justice = new Justice;
        $justice->type = $justiceType;

        return view('justice.create', compact('justice', 'conflict', 'justiceType'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\JusticeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Conflict $conflict, JusticeRequest $request)
    {
        $justiceableParams = $request->input('justiceable') ?? [];
        $dyadicConflictsParams = $request->input('dyadicConflicts') ?? [];
        $metaParams = $request->input('items') ?? [];

        $justiceParams = $request->except(['justiceable', 'dyadicConflicts', 'task', 'items']);

        $task = $request->input('task') ?? null;

        $justice = Justice::create($justiceParams);

        $justice->coding_notes = $justiceParams['coding_notes'];

        $justice->conflict()->associate($conflict);
        $justice->user()->associate(Auth::user());

        if ($relatedDcjid = $request->input('related')) {
            $justice->related = $relatedDcjid;
        }

        $justice->save();

        $justice->createItems($metaParams);

        $justice->dyadicConflicts()->sync($dyadicConflictsParams);

        // todo: put this stuff in a db later.
        // deprecate using process as trait models
        $processDictionary = [
            'trial' => 'T',
            'truth' => 'C',
            'reparation' => 'R',
            'amnesty' => 'A',
            'purge' => 'P',
            'exile' => 'E',
        ];

        $convertedType = $processDictionary[$justice->type];

        $justice->dcjid = $conflict->old_conflict_id.'_'.$conflict->year
            .'_'.$convertedType.'_'.$justice->count;

        $justice->save();

        if ($relatedDcj = $justice->related) {
            $related = Justice::where('dcjid', $relatedDcj)->first();
            $related->related = $justice->dcjid;
            $related->save();
        }

        return redirect()->route('conflict.show', ['conflict' => $conflict, 'task' => $task])
            ->with('status', "$justice->type $justice->dcjid created");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Justice  $justice
     * @return \Illuminate\Http\Response
     */
    public function show(Conflict $conflict, Justice $justice, Request $request)
    {
        $type = $request->input('type');

        return view('justice.show', compact('justice', 'conflict', 'type'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Justice  $justice
     * @return \Illuminate\Http\Response
     */
    public function edit(Conflict $conflict, Justice $justice, Request $request)
    {
        $type = $request->input('type');

        return view('justice.edit', compact('justice', 'conflict', 'type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\JusticeRequest  $request
     * @param  \App\Justice  $justice
     * @return \Illuminate\Http\Response
     */
    public function update(Conflict $conflict, JusticeRequest $request, Justice $justice)
    {
        $justiceableParams = $request->input('justiceable') ?? [];
        $dyadicConflictsParams = $request->input('dyadicConflicts') ?? [];
        $metaParams = $request->input('items') ?? [];

        $justiceParams = $request->except(['justiceable', 'dyadicConflicts', 'task', 'formItems']);

        $task = $request->input('task') ?? null;

        // Later these justice params should be moved into its own 'justice' base form
        // that all the other dcj process forms inherit from
        $foobarParams = collect($justice->getFillable())->mapWithKeys(function ($key) use ($justiceParams) {
            return [
                $key => $justiceParams[$key] ?? null,
            ];
        })->toArray();

        $foobarParams['type'] = $justice->type;

        $justice->fill($foobarParams);
        $justice->coding_notes = $justiceParams['coding_notes'];

        if ($relatedDcjid = $request->input('related')) {
            $justice->related = $relatedDcjid;
        }

        $justice->save();

        $justice->upsertItems($metaParams);

        $justice->dyadicConflicts()->sync($dyadicConflictsParams);

        if ($relatedDcj = $justice->related) {
            $related = Justice::where('dcjid', $relatedDcj)->first();
            $related->related = $justice->dcjid;
            $related->save();
        }

        return redirect()->route('conflict.show', compact('conflict', 'justice', 'task'))
            ->with('status', ucfirst($justice->type)." $justice->dcjid updated");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Justice  $justice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Conflict $conflict, Justice $justice, Request $request)
    {
        Justice::destroy($justice->id);
        $task = $request->input('task') ?? null;

        return redirect()->route('conflict.show', compact('conflict', 'justice', 'task'))
            ->with('status', ucfirst($justice->type)." $justice->dcjid was deleted");
    }
}
