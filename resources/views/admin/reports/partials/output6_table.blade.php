<table id="data-table" class="table is-narrow">
    <thead>
        <tr>
            <th>Course Number</th>
            <th>Course Title</th>
            <th>Academic</th>
            <th>Email</th>
            <th>Request Type</th>
            <th>Start Date</th>
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
            </tr>
        @endforeach
    </tbody>
</table>