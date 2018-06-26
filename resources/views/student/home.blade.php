@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <div class="columns">
      <div class="column">
        <h3 class="title is-3">Available Requests
          <a class="button is-outlined toggle-blurb">
            <span class="icon"><i class="fa fa-info-circle" aria-hidden="true"></i></span>
            <span>info</span>
          </a>
        </h3>
        @include('student.partials.blurb')
      </div>
      <div class="column">
        <student-notes :student='@json(auth()->user())' :degreelevels='@json($degreeLevels)'></student-notes>
      </div>
    </div>
  </div>
</div>
<br>
@include('student.partials.accepted_applications')
@foreach ($courses as $course)
  @if($course->hasRequests() and (!$course->requestsAreAllAccepted() or auth()->user()->isAcceptedOnARequest($course->id)))
    <div class="columns is-centered">
      <div class="column is-three-quarters">
        <div class="card">
          <header class="card-header">
            <p class="card-header-title is-gla-header">{{ $course->code }} {{ $course->title }}</p>
          </header>
          <div class="requests-content-{{$course->id}}">
            <div class="card-content">
              @foreach ($course->staff as $staff)
                <div class="columns">
                  @foreach($staff->requestsForCourse($course) as $request)
                    @if ($request->start_date and (!$request->isFull() or auth()->user()->isAcceptedOnARequest($course->id)))
                      <demonstrator-request :request="{{ $request->forVue() }}"></demonstrator-request>
                    @endif
                  @endforeach
                </div>
                @if(!($staff == $course->staff->last())) <hr> @endif
              @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif
@endforeach
@endsection