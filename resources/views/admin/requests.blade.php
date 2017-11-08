@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h3 class="title is-3">All Requests by Staff</h3>
  </div>
</div>
@if ($noRequests)
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h5 class="subtitle is-5">There are no requests currently. Please <a href="{{ route('import.index') }}">import</a> some.</h5>
  </div>
</div>
@else
  @foreach ($staff as $staffmember)
    @if($staffmember->courses->count())
    <div class="columns is-centered">
      <div class="column is-three-quarters">
        <div class="card">
          <header class="card-header">
            <p class="card-header-title is-gla-header">
              {{ $staffmember->fullName }} ({{ $staffmember->courses->count() }} {{str_plural('course', $staffmember->courses->count())}})
            </p>
          </header>
          <div class="card-content">
            <div class="content">
              @foreach ($staffmember->courses as $course)
              <div class="columns is-centered">
                <div class="column">
                  <div class="card">
                    <header class="card-header tabs is-fullwidth">
                      <ul>
                        <li class="is-active"><a class="requests-tab" data-course="{{ $course->id }}">{{ $staffmember->fullName }} - {{ $course->code }} {{ $course->title }}</a></li>
                        <li class="is-pulled-right"><a class="applicants-tab" data-course="{{ $course->id }}">Applicants</a></li>
                      </ul>
                    </header>
                    <div class="requests-content-{{$course->id}}">
                      <div class="card-content">
                        <div class="columns">
                          <staff-request :request="{{ $staffmember->requestsForUserCourse($course->id, 'Demonstrator')->toJson() }}"></staff-request>
                          <staff-request :request="{{ $staffmember->requestsForUserCourse($course->id, 'Tutor')->toJson() }}"></staff-request>
                          <staff-request :request="{{ $staffmember->requestsForUserCourse($course->id, 'Marker')->toJson() }}"></staff-request>
                        </div>
                      </div>
                    </div>
                    <div class="applicants-content-{{$course->id}}" style="display:none">
                      <div class="card-content">
                        <h4 class="title is-4">Students who have applied</h4>
                        <p class="subtitle">Click the toggle button to accept a student {{$staffmember->id}}</p>
                        @foreach ($course->applicationsForUser($staffmember->id) as $application)
                          <student-application :application="{{ $application->forVue() }}"></student-application>
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif
  @endforeach
@endif
@endsection