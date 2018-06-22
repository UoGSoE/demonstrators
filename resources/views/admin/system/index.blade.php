@extends('layouts.app')

@section('content')
<div class="columns is-centered">
    <div class="column is-three-quarters">
        <h1 class="title">System Settings</h1>
        <hr>
        <form method="POST" action="{{ route('admin.system.expired_contracts') }}">
            @csrf
            <div class="is-normal"><label class="label">Delete students whose contracts have expired before:</label></div>
            <div class="field has-addons">
                <p class="control has-icons-left">
                    <input required class="input is-small" type="date" name="contract_expiration" placeholder="Contract Start Date">
                    <span style="margin-top:4px" class="icon is-small is-left"><i class="fa fa-calendar fa-calendar-vue"></i></span>
                </p>
                <p class="control">
                    <button class="button is-danger is-small">
                        Submit
                    </button>
                </p>
            </div>
            <p class="help">Removes students from the system that have contracts that expired before the date you provide. This will also remove their applications and email logs.</p>
        </form>
        <hr>
        <form method="POST" action="{{ route('admin.system.reset_requests') }}">
            @csrf
            <div class="is-normal"><label class="label">Reset all job requests' start dates and removes their applications that started before:</label></div>
            <div class="field has-addons">
                <p class="control has-icons-left">
                    <input required class="input is-small" type="date" name="request_start" placeholder="Request Start Date">
                    <span style="margin-top:4px" class="icon is-small is-left"><i class="fa fa-calendar fa-calendar-vue"></i></span>
                </p>
                <p class="control">
                    <button class="button is-danger is-small">
                        Submit
                    </button>
                </p>
            </div>
            <p class="help">Resets the job request start date to be empty that started before the date you provide. This will also remove the request's applications.</p>
        </form>
        <hr>
    </div>
</div>
@endsection