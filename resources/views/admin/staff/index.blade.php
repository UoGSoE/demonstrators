@extends('layouts.app')

@section('content')
<div class="columns is-centered">
    <div class="column is-three-quarters">
        <table id="data-table" class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>GUID</th>
                    <th>e-mail</th>
                    <th>Courses</th>
                    <th>Requests</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staff as $staffmember)
                    <tr>
                        <td>{{$staffmember->surname}}, {{$staffmember->forenames}}</td>
                        <td>{{$staffmember->username}}</td>
                        <td>
                            <a href="mailto:{{$staffmember->email}}">
                                {{$staffmember->email}}
                            </a>
                        </td>
                        <td>
                            {{$staffmember->courses->count()}} {{str_plural('course', $staffmember->courses->count())}}
                        </td>
                        <td>
                            {{$staffmember->requests->count()}} {{str_plural('request', $staffmember->requests->count())}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection