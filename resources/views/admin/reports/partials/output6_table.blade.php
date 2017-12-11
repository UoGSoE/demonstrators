<table id="data-table" class="table is-narrow">
    <thead>
        <tr>
            <th>Course Number</th>
            <th>Course Title</th>
            <th>Academic</th>
            <th>Email</th>
            <th>Request Type</th>
            <th>Start Date</th>
            <th>Number of Assistants Requested</th>
            <th>Number of Confirmed Students</th>
            <th>Number of Unseen Applications</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($applications as $application)
            <tr>
                <td>{{$application->request->course->code}}</td>
                <td>{{$application->request->course->title}}</td>
                <td>{{$application->request->staff->fullName}}</td>
                <td>{{$application->request->staff->email}}</td>
                <td>{{$application->request->type}}</td>
                <td>{{$application->request->getFormattedStartDate()}}</td>
                <td>{{$application->request->demonstrators_needed}}
                <td>{{$application->request->applications()->accepted()->confirmed()->count()}}</td>
                <td>{{$application->request->applications()->unaccepted()->unseen()->count()}}</td>
            </tr>
        @endforeach
    </tbody>
</table>