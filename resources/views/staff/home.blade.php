@extends('layouts.app')

@section('content')
<div>
    @foreach (Auth()->user()->courses as $course)
        <h3 class="title is-3">{{ $course->code }} {{ $course->title }}</h3>
        @foreach($course->applications() as $application)
            @if ($application->request->staff->id == Auth()->user()->id)
                <p>{{ $application->student->forenames }} {{ $application->student->surname }}</p>
            @endif
        @endforeach
    @endforeach
</div>
@endsection