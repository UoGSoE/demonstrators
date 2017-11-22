@extends('layouts.app')

@section('content')
@include('staff.partials.blurb')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h3 class="title is-3">Teaching Assistant Requests
      <a class="button is-outlined toggle-blurb">
        <span class="icon"><i class="fa fa-info-circle" aria-hidden="true"></i></span>
        <span>info</span>
      </a>
    </h3>
    @if (Auth()->user()->courses->isEmpty())
        <h5 class="subtitle is-5">You are not an academic for any courses listed in this system. If this is incorrect, please email the teaching office.</h5>
    @else
      @if (Auth()->user()->hasEmptyDates())
        <div class="notification is-danger">
          You have submitted requests that do not have a start date filled in. Please fill this in, otherwise the request will not be visible to the students.
        </div>
      @endif
      @foreach (Auth()->user()->courses as $course)
        <div class="card">
          <header class="card-header tabs is-fullwidth">
            <ul>
              <li class="is-active"><a class="requests-tab" data-course="{{ $course->id }}">{{ $course->code }} {{ $course->title }}</a></li>
              <li class="is-pulled-right"><a class="applicants-tab" data-course="{{ $course->id }}" data-user="{{ Auth()->user()->id }}">Applicants</a></li>
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
              <p class="subtitle">Click the toggle button to accept a student (<span class="icon"><i class="fa fa-file-text-o" title="Has contract"></i></span> icon means they already have a contract in place)</p>
              @foreach ($course->applicationsForUser(Auth()->user()->id) as $application)
                <student-application :application="{{ $application->forVue() }}"></student-application>
              @endforeach
            </div>
          </div>
        </div>
        <br>
      @endforeach
    @endif
  </div>
</div>
@endsection