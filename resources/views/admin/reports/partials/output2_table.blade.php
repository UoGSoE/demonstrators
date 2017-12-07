<table id="data-table" class="table is-narrow">
    <thead>
        <tr>
            <th>Course Number</th>
            <th>Course Title</th>
            <th>Academic Name</th>
            <th>Academic Email</th>
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
        @foreach ($courses as $course)
            @foreach ($course->staff as $staff)
                @foreach ($staff->requestsForCourse($course) as $request)
                    @foreach ($request->applications as $application)
                        <tr>
                            <td>{{$application->request->course->code}}</td>
                            <td>{{$application->request->course->title}}</td>
                            <td>{{$application->request->staff->fullName}}</td>
                            <td>{{$application->request->staff->email}}</td>
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
            @endforeach
        @endforeach
    </tbody>
</table>