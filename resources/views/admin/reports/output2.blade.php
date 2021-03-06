@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column">
    <h3 class="title is-3">Applications (By Course)
        <a class="button is-gla is-outlined is-pulled-right" href="{{route('admin.reports.output2.download')}}">
            <span class="icon is-small">
            <i class="fa fa-download" aria-hidden="true"></i>
            </span>
        </a>
    </h3>
  </div>
</div>
@foreach ($courses as $course)
    <div class="columns is-centered">
        <div class="column">
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title is-gla-header">{{$course->code}} {{$course->title}}</p>
                </header>
                <div class="card-content">
                    @foreach ($course->staff as $staff)
                    <div class="card">
                        <header class="card-header">
                            <p class="card-header-title is-gla-header">{{$staff->fullName}} - {{$staff->email}}</p>
                        </header>
                        <div class="card-content">
                            <h5 class="title is-5">Students Applied</h5>
                            <table class="table is-narrow is-fullwidth">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Student Email</th>
                                        <th>EWP Documents Sent To Student</th>
                                        <th>RTW Received Confirmation Sent</th>
                                        <th>Contract Email Sent</th>
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
                                        @foreach ($request->applications as $application)
                                            <tr>
                                                <td>{{$application->student->fullName}}</td>
                                                <td>{{$application->student->email}}</td>
                                                <td>{{$application->student->getDateOf('StudentRTWInfo')}}</td>
                                                <td>{{$application->student->getDateOf('StudentRTWReceived')}}</td>
                                                <td>{{$application->student->getDateOf('StudentContractReady')}}</td>
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
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection