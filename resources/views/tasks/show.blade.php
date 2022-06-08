@extends('layouts.app')

@section('content')

    <h1 class="text-break">
        {{  __('tasks.View a task') . ": " . $task->name }}
        <a href="{{ route('tasks.edit', ['task' => $task->id]) }}">&#9881;</a>
    </h1>
    <p class="text-break">{{  __('tasks.Name') . ": " . $task->name }}</p>
    <p class="text-break">{{ __('taskStatuses.Status') . ": " . $task->status->name }}</p>
    <p class="text-break">{{ __('tasks.Description') . ": " . $task->description }}</p>


    @if ($task->labels()->exists())
        <p>{{ __('labels.Labels') . ": " }}</p>
        <ul>
            @foreach ($task->labels as $label)
                <li>{{ $label->name }}</li>
            @endforeach
        </ul>
    @endif
@endsection('content')
