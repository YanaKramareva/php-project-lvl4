<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\TaskStatus;
use App\Models\User;
use App\Models\Label;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $taskStatuses = TaskStatus::pluck('name', 'id')->all();
        $users = User::pluck('name', 'id')->all();

        $tasks = QueryBuilder::for(Task::class)
            ->allowedFilters(
                [
                AllowedFilter::exact('status_id'),
                AllowedFilter::exact('created_by_id'),
                AllowedFilter::exact('assigned_to_id')
                ]
            )
            ->orderBy('id', 'asc')
            ->paginate();

        $filter = $request->filter ?? null;

        return view('tasks.index', compact('tasks', 'taskStatuses', 'users', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $task = new Task();
        $taskStatuses = TaskStatus::pluck('name', 'id')->all();
        $labels = Label::pluck('name', 'id')->all();
        $executors = User::pluck('name', 'id')->all();

        return view('tasks.create', compact('task', 'taskStatuses', 'labels', 'executors'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $taskInputData = $request->validate(
            [
            'name' => 'required|max:255|unique:tasks',
            'status_id' => 'required',
            'description' => 'nullable|string',
            'assigned_to_id' => 'nullable|integer',
            'labels' => 'nullable|array'
            ],
            $messages = [
            'unique' => __('validation.The task name has already been taken'),
            'max' => __('validation.The name should be no more than :max characters'),
            ]
        );

        $user = Auth::user();
        $task = $user->tasks()->make();
        $task->fill($taskInputData);
        $task->save();

        $labels = collect($request->input('labels'))->filter(fn($label) => isset($label));
        $task->labels()->attach($labels);

        flash(__('tasks.Task has been added successfully'))->success();
        return redirect()->route('tasks.index');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task $task
     * @return Response
     */
    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task $task
     * @return Response
     */
    public function edit(Task $task)
    {
        $taskStatuses = TaskStatus::pluck('name', 'id')->all();
        $labels = Label::pluck('name', 'id')->all();
        $executors = User::pluck('name', 'id')->all();

        return view('tasks.edit', compact('task', 'taskStatuses', 'executors', 'labels'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Task         $task
     * @return Response
     */
    public function update(Request $request, Task $task)
    {
        $taskInputData = $this->validate(
            $request,
            [
            'name' => 'required|max:255|unique:tasks,name,' . $task->id,
            'description' => 'nullable|string',
            'status_id' => 'required',
            'assigned_to_id' => 'nullable|integer',
            'labels' => 'nullable|array'
            ],
            $messages = [
            'unique' => __('validation.The task name has already been taken'),
            'max' => __('validation.The name should be no more than :max characters'),
            ]
        );

        $task->fill($taskInputData);
        $task->save();

        $labels = collect($request->input('labels'))->filter(fn($label) => isset($label));
        $task->labels()->sync($labels);

        flash(__('tasks.Task has been updated successfully'))->success();
        return redirect()->route('tasks.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task $task
     * @return Response
     */
    public function destroy(Task $task)
    {
        $task->labels()->detach();
        $task->delete();

        flash(__('tasks.Task has been deleted successfully'))->success();
        return redirect()->route('tasks.index');
    }
}
