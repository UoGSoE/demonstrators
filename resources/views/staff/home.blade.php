@extends('layouts.app')

@section('content')
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
              @include('staff.partials.requests_tab', ['request' => Auth()->user()->requestsForUserCourse($course->id, 'Demonstrator')])
              @include('staff.partials.requests_tab', ['request' => Auth()->user()->requestsForUserCourse($course->id, 'Tutor')])
              @include('staff.partials.requests_tab', ['request' => Auth()->user()->requestsForUserCourse($course->id, 'Marker')])
            </div>
          </div>
        </div>
        <div class="applicants-content-{{$course->id}}" style="display:none">
          <div class="card-content">
            <h4 class="title is-4">Students who have applied</h4>
            <p class="subtitle">Click the toggle button to accept a student</p>
            @foreach ($course->applications() as $application)
              <article class="media">
                <div class="media-left">
                  <label class="switch">
                    <input data-application="{{ $application->id }}" class="applicants-checkbox" type="checkbox" value="1" @if ($application->isAccepted()) checked @endif>
                    <span class="slider round"></span>
                  </label>
                </div>
                <div class="media-content">
                  <div class="content">
                    <p>
                      <strong>{{ $application->student->fullName }}</strong> <small>{{ $application->student->email }}</small>
                      <br>
                      {{ $application->request->type }} - for {{ $application->maximum_hours }} {{ str_plural('hour', $application->maximum_hours) }}
                      <br>
                      {{ $application->student->notes }}
                    </p>
                  </div>
                </div>
              </article>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
@endforeach
@endsection