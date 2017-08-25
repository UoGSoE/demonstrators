@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h3 class="title is-3">Demonstrator Requests</h3>
  </div>
</div>
@if (Auth()->user()->courses->isEmpty())
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h5 class="subtitle is-5">You are not an academic for any courses listed in this system. If this is incorrect, please email the teaching office.</h5>
  </div>
</div>
@else
  @foreach (Auth()->user()->courses as $course)
    <div class="columns is-centered">
      <div class="column is-three-quarters">
        <div class="card">
          <header class="card-header tabs is-fullwidth">
            <ul>
              <li class="is-active"><a class="requests-tab" data-course="{{ $course->id }}">{{ $course->code }} {{ $course->title }}</a></li>
              <li class="is-pulled-right"><a class="applicants-tab" data-course="{{ $course->id }}">Applicants</a></li>
            </ul>
          </header>
          <div class="requests-content-{{$course->id}}">
            <div class="card-content">
              <div class="columns">
                <staff-request :request="{{ Auth()->user()->requestsForUserCourse($course->id, 'Demonstrator')->toJson() }}"></staff-request>
                <staff-request :request="{{ Auth()->user()->requestsForUserCourse($course->id, 'Tutor')->toJson() }}"></staff-request>
                <staff-request :request="{{ Auth()->user()->requestsForUserCourse($course->id, 'Marker')->toJson() }}"></staff-request>
              </div>
            </div>
          </div>
          <div class="applicants-content-{{$course->id}}" style="display:none">
            <div class="card-content">
              <h4 class="title is-4">Students who have applied</h4>
              <p class="subtitle">Click the toggle button to accept a student</p>
              @foreach ($course->applications() as $application)
                <student-application :application="{{ $application->forVue() }}"></student-application>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  @endforeach
@endif
@endsection