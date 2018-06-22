@extends('layouts.app')

@section('content')
<div class="columns is-centered">
    <div class="column is-three-quarters">
        <h1 class="title">System Settings</h1>
        <hr>
        <h5 class="title is-5">Clear Old Data</h5>
        <p class="help">Removes students from the system with contracts that have ended before the date you provide.</p>
        <p class="help">Also resets job requests start date and removes applications for all job requests that started before the date you provide.</p>
        <br>
        <form method="POST" action="{{ route('admin.system.next_year') }}">
            @csrf
            <div class="is-normal"><label class="label">Remove students whose contracts have expired before:</label></div>
            <p class="control is-expanded has-icons-left">
                <input required class="input is-inline is-small" type="date" name="contract_expiration" placeholder="Contract Start Date">
                <span style="margin-top:4px" class="icon is-small is-left"><i class="fa fa-calendar fa-calendar-vue"></i></span>
            </p>
            <br>
            <div class="is-normal"><label class="label">Remove applications from requests whose start dates are before:</label></div>
            <p class="control is-expanded has-icons-left">
                <input required class="input is-inline is-small" type="date" name="request_start" placeholder="Request Start Date">
                <span style="margin-top:4px" class="icon is-small is-left"><i class="fa fa-calendar fa-calendar-vue"></i></span>
                <p class="help">This will also reset demonstrator request start dates</p>
            </p>
            <br>
            <button class="button is-danger">Submit</button>
        </form>
        <hr>
    </div>
</div>
@endsection