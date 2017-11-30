@extends('layouts.app')

@section('content')

<div class="columns is-centered">
  <div class="column is-three-quarters">
    <div class="columns">
      <div class="column">
        <h3 class="title is-3">Teaching Assistant Requests
          <a class="button is-outlined toggle-blurb">
            <span class="icon"><i class="fa fa-info-circle" aria-hidden="true"></i></span>
            <span>info</span>
          </a>
        </h3>
      </div>
    </div>
    @include('staff.partials.blurb')
    @if (Auth()->user()->courses->isEmpty())
        <h5 class="subtitle is-5">You are not an academic for any courses listed in this system. If this is incorrect, please email the teaching office.</h5>
    @else
      @if (Auth()->user()->hasEmptyDates())
        <div class="notification is-danger empty-dates">
          You have submitted requests that do not have a start date filled in. Please fill this in, otherwise the request will not be visible to the students.
        </div>
      @endif
      @foreach (Auth()->user()->courses as $course)
        <div class="card">
          <header class="card-header tabs is-fullwidth">
            @if (auth()->user()->username != 'mh5b')
              <ul role="tablist">
                <li class="is-active" role="tab"><a class="requests-tab" data-course="{{ $course->id }}">{{ $course->code }} {{ $course->title }}</a></li>
                <li class="is-pulled-right" role="tab"><a class="applicants-tab" data-course="{{ $course->id }}" data-user="{{ Auth()->user()->id }}">Applicants</a></li>
              </ul>
            @else
              <p class="card-header-title">{{ $course->code }} {{ $course->title }}</p>
            @endif
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
          <div class="applicants-content-{{$course->id}}" @if (auth()->user()->username != 'mh5b') style="display:none" @endif>
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