@extends('layouts.app')
{{var_dump(session()->get('compareSchedule'))}}
@section('content')
<form method="POST" action="{{ route('skills.compare.store') }}">
    @csrf
    <input value="{{ $opA->id.'-'.$opB->id }}" name="compareSet">
    <button type="submit" class="btn btn-primary" name="winner" value="{{ $opA->id }}">{{ $opA->name }}</button>
    <button type="submit" class="btn btn-danger" name="winner" value="{{ $opB->id }}">{{ $opB->name }}</button>
</form>
@endsection