<table id="data-table" class="table is-narrow">
    <thead>
        <tr>
            <th>Subject</th>
            <th>Cat</th>
            <th>Start Date</th>
            <th>Long Title</th>
            <th>Associated Academic</th>
            <th>Main Ac GUID</th>
            <th>Email</th>
            <th>Academic First Name</th>
            <th>Add Ac Staff Req</th>
            <th>Ad Ac Staff Name</th>
            <th>No. Demos Requested</th>
            <th>No. Demos Unfilled</th>
            <th>Hours / Demo</th>
            <th>Train / Demo</th>
            <th>No. Tutors Requested</th>
            <th>No. Tutors Unfilled</th>
            <th>Hours / Tutor</th>
            <th>Train / Tutor</th>
            <th>No. Markers Requested</th>
            <th>No. Markers Unfilled</th>
            <th>Hours / Marker</th>
            <th>Train / Marker</th>
            <th>Same person as Add Ac ?</th>
            <th>Activity Type</th>
            <th>Dual Activity</th>
            <th>Same person for dual</th>
            <th>Lab content / subject</th>
            <th>Special Requirements</th>
            <th>Semester</th>
            <th>Ac Response</th>
            <th>Notes</th>
            <th>Open to Y4 / 5 / PGT</th>
            <th>Offered</th>
            <th>Respondents</th>
            <th>Messages</th>
            <th>Referred</th>
            <th>Filled</th>
            <th>Contract Email</th>
            <th>RtW / EWP sent</th>
            <th>Contract Produced</th>
            <th>Contract Returned</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($requests as $request)
            <tr>
                <td>{{ $request->course->subject }}</td>
                <td>{{ $request->course->catalogue }}</td>
                <td>{{ $request->start_date }}</td>
                <td>{{ $request->course->title }}</td>
                <td>{{ $request->staff->fullName }}</td>
                <td>{{ $request->staff->username }}</td>
                <td>{{ $request->staff->email }}</td>
                <td>{{ $request->staff->forenames }}</td>
                <td></td>
                <td></td>
                <td>@if ($request->type == 'Demonstrator') {{ $request->demonstrators_needed }} @endif</td>
                <td>@if ($request->type == 'Demonstrator') {{ $request->getNumberUnfilled() }} @endif</td>
                <td>@if ($request->type == 'Demonstrator') {{ $request->hours_needed }} @endif</td>
                <td>@if ($request->type == 'Demonstrator') {{ $request->hours_training }} @endif</td>
                <td>@if ($request->type == 'Tutor') {{ $request->demonstrators_needed }} @endif</td>
                <td>@if ($request->type == 'Tutor') {{ $request->getNumberUnfilled() }} @endif</td>
                <td>@if ($request->type == 'Tutor') {{ $request->hours_needed }} @endif</td>
                <td>@if ($request->type == 'Tutor') {{ $request->hours_training }} @endif</td>
                <td>@if ($request->type == 'Marker') {{ $request->demonstrators_needed }} @endif</td>
                <td>@if ($request->type == 'Marker') {{ $request->getNumberUnfilled() }} @endif</td>
                <td>@if ($request->type == 'Marker') {{ $request->hours_needed }} @endif</td>
                <td>@if ($request->type == 'Marker') {{ $request->hours_training }} @endif</td>
                <td></td>
                <td>{{ $request->type }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $request->skills }}</td>
                <td>{{ $request->getSemesters() }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>