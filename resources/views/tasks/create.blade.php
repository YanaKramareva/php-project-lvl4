@extends('layouts.app')

@section('content')
    <h1 class="mb-5">{{ __('tasks.Create task') }}</h1>
    {{Form::open(['url' => route('tasks.store'), 'class' => 'w-50'])}}
    <div class="form-group mb-3">
        {{Form::label('name', __('tasks.Name'))}}
        {{Form::text('name', $task->name, ['class' => 'form-control'])}}
        @if ($errors->has('name'))
            @error('name')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        @endif
    </div>
    <div class="form-group mb-3">
        {{Form::label('description', __('tasks.Description'))}}
        {{Form::textarea('description', null, ['class' => 'form-control', 'cols' => '50', 'rows' => '10'])}}
    </div>
    <div class="form-group mb-3">
        {{Form::label('status_id', __('taskStatuses.Status'))}}
        {{Form::select('status_id', $taskStatuses, null, ['placeholder' => '----------', 'class' => 'form-control'])}}
        @if ($errors->has('status_id'))
            @error('status_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        @endif
    </div>
    <div class="form-group mb-3">
        {{Form::label('assigned_to_id', __('tasks.Executor'))}}
        {{Form::select('assigned_to_id', $executors, null, ['placeholder' => '----------', 'class' => 'form-control'])}}
        <div class="form-group mb-3">
            {{Form::label('label_id', __('labels.Labels'))}}
            {{Form::select('label_id', $labels, $task->labels, ['placeholder' => '', 'multiple' => 'multiple', 'name' => 'labels[]', 'class' => 'form-control'])}}
        </div>
    {{Form::submit(__('tasks.Create'), ['class' => 'btn btn-info mt-3'])}}
    {{Form::close()}}
@endsection
