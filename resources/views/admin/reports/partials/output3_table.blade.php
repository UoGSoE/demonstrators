<table id="data-table" class="table is-narrow is-fullwidth">
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Student Email</th>
            <th>RTW Email Sent</th>
            <th>Contract Email Sent</th>
            <th>Total No. of Hours</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($students as $student)
            <tr>
                <td>{{$student->fullName}}</td>
                <td>{{$student->email}}</td>
                <td>{{$student->getDateOf('StudentRTWReceived')}}</td>
                <td>{{$student->getDateOf('StudentContractReady')}}</td>
                <td>{{$student->getTotalConfirmedHours()}}</td>
            </tr>
        @endforeach
    </tbody>
</table>                