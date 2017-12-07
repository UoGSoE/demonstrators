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
        @foreach ($requests as $request)
            <tr>
                <td>{{$request->course->code}}</td>
                <td>{{$request->course->title}}</td>
                <td>{{$request->staff->fullName}}</td>
                <td>{{$request->staff->email}}</td>
                <td>{{$request->type}}</td>
                <td>{{$request->getFormattedStartDate()}}</td>
            </tr>
        @endforeach
    </tbody>
</table>