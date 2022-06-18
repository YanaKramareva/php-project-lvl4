{{Form::select($name, $value, null, array_merge(['placeholder' => '----------', 'class' => 'form-control'], $attributes))}}
@if ($errors->has($name))
    @error($name)
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
@endif
