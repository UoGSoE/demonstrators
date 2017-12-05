<table id="data-table" class="table is-narrow">
    <thead>
        <tr>
            <th>Course Number</th>
            <th>Course Title</th>
            <th>Academic Name</th>
            <th>Academic Email</th>
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
        @foreach ($courses as $course)
            @foreach ($course->staff as $staff)
                @if($staff->academicHasAcceptedApplicationsForCourse($course))
                    @foreach ($staff->requestsForCourse($course) as $request)
                        @foreach ($request->applications()->accepted()->get() as $application)
                            <tr>
                                <td>{{$application->request->course->code}}</td>
                                <td>{{$application->request->course->title}}</td>
                                <td>{{$application->request->staff->fullName}}</td>
                                <td>{{$application->request->staff->email}}</td>
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
                @endif
            @endforeach
        @endforeach
    </tbody>
</table>