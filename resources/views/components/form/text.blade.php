{{Form::text($name, $value, array_merge(['class' => 'form-control'], $attributes))}}
@if ($errors->has($name))
    @error($name)
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
@endif
