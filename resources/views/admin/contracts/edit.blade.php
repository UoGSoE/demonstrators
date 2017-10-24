@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h3 class="title is-3">Student Applications</h3>
  </div>
</div>
@if ($noApplications)
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h5 class="subtitle is-5">No students have applied for any requests yet.</h5>
  </div>
</div>
@else
  @foreach ($students as $student)
  <div class="columns is-centered">
    <div class="column is-three-quarters">
      <div class="card">
        <header class="card-header">
          <p class="card-header-title">
            {{ $student->fullName }}
          </p>
          <form method="POST" action="{{ route('admin.mega_delete') }}">
            {{ csrf_field() }}
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            <button style="margin:10px" class="button is-gla-danger is-outlined is-small mega-delete card-header-icon">
              <span class="icon is-small">
                <i class="fa fa-times" aria-hidden="true"></i>
              </span>
            </button>
          </form>
        </header>
        <div class="card-content">
          <div class="media student-media">
            <div class="media-left">
              <div class="field is-horizontal">
                <label class="switch">
                  <input data-user="{{ $student->id }}" class="rtw-checkbox" type="checkbox" id="togBtn" value="1" @if ($student->returned_rtw) checked @endif>
                  <div class="slider round"></div>
                </label>
                <label class="label slider-label">RTW</label>
              </div>
              <div class="field is-horizontal">
                <label class="switch">
                  <input data-user="{{ $student->id }}" class="contracts-checkbox" type="checkbox" id="togBtn" value="1" @if ($student->has_contract) checked @endif>
                  <div class="slider round"></div>
                </label>
                <label class="label slider-label">Contract</label>
              </div>
            </div>
            <div class="media-content student-media-content">
              <span class="icon is-small">
                <i class="fa fa-id-card-o" aria-hidden="true"></i>
              </span>
              <strong> GUID: </strong>{{ $student->username }}<br>
              <span class="icon is-small">
                <i class="fa fa-envelope-o" aria-hidden="true"></i>
              </span>
              <strong> Email: </strong><a href="mailto:{{ $student->email }}">{{ $student->email }}</a><br>
              @if ($student->notes)
                <span class="icon is-small">
                  <i class="fa fa-pencil" aria-hidden="true"></i>
                </span>
                <strong> Notes: </strong>{{ $student->notes}}<br>
              @endif
            </div>
            @if ($student->hasApplications())
              <div class="content student-content">
                <form method="POST" action="{{ route('admin.manual_withdraw') }}">
                  {{ csrf_field() }}
                  <input type="hidden" name="student_id" value="{{ $student->id }}">
                  <table class="table is-narrow">
                    <thead>
                      <tr>
                        <th>Applied Position</th>
                        <th>Course</th>
                        <th>Owner</th>
                        <th>Hours</th>
                        <th>Accepted?</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($student->applications as $application)
                        <tr>
                          <td>{{ $application->request->type }}</td>
                          <td>{{ $application->request->course->fullTitle }}</td>
                          <td>{{ $application->request->staff->fullName }}</td>
                          <td>{{ $application->request->hours_needed }}</td>
                          <td>{{ $application->is_accepted ? "Yes" : "No" }}</td>
                          <td>
                            <input name="applications[]" type="checkbox" value="{{ $application->id }}">
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                  <button class="button is-small is-gla-danger is-pulled-right">Withdraw selected</button>
                </form>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
  @endforeach
@endif
@endsection