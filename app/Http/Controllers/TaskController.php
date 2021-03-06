<?php

namespace App\Http\Controllers;

use App\ConflictSeries;
use App\Task;
use App\User;
use Auth;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $me = Auth::user();
        $status = $request->query('status') ?? 'all';
        $viewType = $request->query('view_type') ?? null;

        $tasks = Task::latest('updated_at')->with('user', 'assigner', 'conflictSeries.episodes')
            ->withCount('conflictEpisodes', 'conflictJustices');

        if ($status != 'all') {
            $tasks = $tasks->where('status', $status);
        }

        if ($region = trim($request->query('region'))) {
            $conflictSeries = ConflictSeries::with('episodes')->get()->filter(function ($cs) use ($region) {
                return $region == $cs->region;
            });

            $tasks = $tasks->whereIn('conflict_ucdp_id', $conflictSeries->pluck('id'));
        }

        $tasks = $tasks->latest('updated_at')
            ->paginate(30);

        return view('task.index', compact('tasks', 'status', 'viewType'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::select('id', 'name', 'email')->get()->mapWithKeys(function ($user) {
            return [$user['id'] => $user['name'].' -- '.$user['email']];
        });

        return view('task.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'conflict_series' => 'exists:conflict_series,id',
            'user' => 'required|array|min:1',
        ]);

        $usersInputs = collect($request->input('user'));

        $statuses = $usersInputs->map(function ($userInput) use ($request) {
            $redundantTask = Task::where([
                ['conflict_ucdp_id', $request->input('conflict_series')],
                ['user_id', $userInput],
            ])->exists();

            $user = User::find($userInput);

            if ($redundantTask) {
                return $user->name.' was already assigned to UCDP #'.$request->input('conflict_series');
            } else {
                $task = new Task;

                $task->user_id = $user->id;
                $task->conflict_ucdp_id = $request->input('conflict_series');
                $task->created_by = Auth::id();
                $task->save();

                return 'UCPD #'.$task->conflict_ucdp_id.' assigned to '.$task->user->name;
            }
        });

        return redirect()->route('task.index')
            ->with('status', $statuses);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $status = $request->input('status') ?? false;
        $task->status = $status;
        $task->save();

        $flash = 'Task -- '.$task->conflictSeries->name.' -- ';

        if ($status == 1) {
            $flash .= 'marked as completed.';
        } elseif ($status == 2) {
            $flash .= 'locked for review.';
        } elseif ($status == 3) {
            $flash .= 'finalized -- coded and reviewed!';
        } elseif ($status == 0) {
            $flash .= "changed back to 'in progress.'";
        }

        return redirect(url()->previous())->with('status', $flash);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();
        $flash = 'Task '.$task->name.' for '.$task->user->name.' deleted';

        return redirect(url()->previous())->with('status', $flash);
    }
}
