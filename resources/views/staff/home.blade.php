@extends('layouts.app')

@section('content')

@foreach (Auth()->user()->courses as $course)
<div class="columns is-centered">
    <div class="column is-three-quarters">
        <div class="card">
            <header class="card-header">
                <div class="tabs is-centered is-boxed">
                  <ul>
                    <li class="is-active"><a class="requests-tab" data-course="{{ $course->id }}">{{ $course->code }} {{ $course->title }}</a></li>
                    <li class="is-pulled-right"><a class="applicants-tab" data-course="{{ $course->id }}">Applicants</a></li>
                  </ul>
                </div>
            </header>
            <div class="requests-content-{{$course->id}}">
                <div class="card-content">
                        <div class="columns">
                            @foreach(Auth()->user()->requestsForUserCourse($course->id) as $request)
                                <div class="column is_one_third">
                                    <h5 class="title is-5">{{ $request->type }}</h5>
                                    <label class="label">Hours</label>
                                    <div class="field">
                                        <p class="control is-expanded has-icons-left">
                                            <input name="hours_needed" class="input is-small" type="numeric" placeholder="Hours" value="{{ $request->hours_needed }}">
                                            <span class="icon is-small is-left">
                                                <i class="fa fa-clock-o"></i>
                                            </span>
                                        </p>
                                    </div>
                                    <label class="label">Demonstrators</label>
                                    <div class="field">
                                        <p class="control is-expanded has-icons-left">
                                            <input name="demonstrators_needed" class="input is-small" type="numeric" placeholder="Demonstrators" value="{{ $request->demonstrators_needed }}">
                                            <span class="icon is-small is-left">
                                                <i class="fa fa-users"></i>
                                            </span>
                                        </p>
                                    </div>
                                    <label class="label">Semesters</label>
                                    <div class="field">
                                        <label class="checkbox">
                                          <input name="semester_1" type="checkbox" @if ($request->semester_1) checked @endif>
                                          1
                                        </label>
                                        <label class="checkbox">
                                          <input name="semester_2" type="checkbox" @if ($request->semester_2) checked @endif>
                                          2
                                        </label>
                                        <label class="checkbox">
                                          <input name="semester_3" type="checkbox" @if ($request->semester_3) checked @endif>
                                          3
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                </div>
                <footer class="card-footer">
                    <button class="button is-success card-footer-item">Save</button>
                </footer>
            </div>
            <div class="applicants-content-{{$course->id}}" style="display:none">
                <div class="card-content">
                @foreach ($course->applications() as $application)
                    <article class="media">
                        <div class="media-left">
                          <label class="switch">
                            <input data-application="{{ $application->id }}" class="checkbox" type="checkbox" value="1" @if ($application->isAccepted()) checked @endif>
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