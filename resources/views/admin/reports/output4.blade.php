@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column">
    <h3 class="title is-3">Working Students (By Course)</h3>
  </div>
</div>
@if ($courses->count())
    @foreach ($courses as $course)
        <div class="columns is-centered">
            <div class="column">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title is-gla-header">{{$course->code}} {{$course->title}}</p>
                    </header>
                    <div class="card-content">
                        @foreach ($course->staff as $staff)
                            @if($staff->academicHasAcceptedApplicationsForCourse($course))
                                <div class="card">
                                    <header class="card-header">
                                        <p class="card-header-title is-gla-header">{{$staff->fullName}} - {{$staff->email}}</p>
                                    </header>
                                    <div class="card-content">
                                        <table class="table is-narrow is-fullwidth">
                                            <thead>
                                                <tr>
                                                    <th>Student Name</th>
                                                    <th>Student Email</th>
                                                    <th>Lab Dem Offered</th>
                                                    <th>Lab Dem Accepted</th>
                                                    <th>Marker Offered</th>
                                                    <th>Marker Accepted</th>
                                                    <th>Tutor Offered</th>
                                                    <th>Tutor Accepted</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($staff->requestsForCourse($course) as $request)
                                                    @foreach ($request->applications()->confirmed()->get() as $application)
                                                        <tr>
                                                            <td>{{$application->student->fullName}}</td>
                                                            <td>{{$application->student->email}}</td>
                                                            <td>{{$application->student->getDateOf('AcademicAcceptsStudent', $request, 'Demonstrator')}}</td>
                                                            <td>{{$application->student->getDateOf('StudentConfirm', $request, 'Demonstrator')}}</td>
                                                            <td>{{$application->student->getDateOf('AcademicAcceptsStudent', $request, 'Marker')}}</td>
                                                            <td>{{$application->student->getDateOf('StudentConfirm', $request, 'Marker')}}</td>
                                                            <td>{{$application->student->getDateOf('AcademicAcceptsStudent', $request, 'Tutor')}}</td>
                                                            <td>{{$application->student->getDateOf('StudentConfirm', $request, 'Tutor')}}</td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <br>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
<div class="columns is-centered">
        <div class="column">
            Nothing to display
    </div>
</div>
@endif
@endsection