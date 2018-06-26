@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h3 class="title is-3">Students
      <a class="button is-gla-success is-outlined is-pulled-right add-student" href="{{route('admin.students.create')}}">
        <span class="icon is-small">
          <i class="fa fa-plus-square" aria-hidden="true"></i>
        </span>
      </a>
    </h3>
  </div>
</div>
@include('admin.contracts.rtw_form')
@include('admin.contracts.contract_form')

  @foreach ($students as $student)
  <div class="columns is-centered">
    <div class="column is-three-quarters">
      <div class="card">
        <header class="card-header">
          <p class="card-header-title">
            {{ $student->fullName }}
          </p>
          <form method="POST" action="{{ route('admin.students.destroy') }}">
            {{ csrf_field() }}
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            <button style="margin:10px" class="button is-gla-danger is-outlined is-small delete-student">
              <span class="icon is-small">
                <i class="fa fa-times" aria-hidden="true"></i>
              </span>
            </button>
            <button style="margin:10px; display:none;" class="button is-gla-danger is-outlined is-small confirm-destroy">
              <span class="icon is-small">
                <i class="fa fa-times" aria-hidden="true"></i>
              </span>
            </button>
          </form>
          <a href="{{route('admin.impersonate', $student->id)}}" style="margin:10px" class="button is-small is-primary">Login As</a>
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
              <span class="icon is-small"><i class="fa fa-id-card-o" aria-hidden="true"></i></span>
              <strong> GUID: </strong>{{ $student->username }}<br>
              @if ($student->degreeLevel)
                <span class="icon is-small"><i class="fa fa-graduation-cap" aria-hidden="true"></i></span>
                <strong> Degree Level: </strong>{{ $student->degreeLevel->title }}<br>
              @endif
              <span class="icon is-small"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>
              <strong> Email: </strong><a href="mailto:{{ $student->email }}">{{ $student->email }}</a><br>
              <span class="rtw-start-{{$student->id}}" @if (!$student->returned_rtw) style="display:none" @endif>
                <span class="icon is-small"><i class="fa fa-calendar-check-o" aria-hidden="true"></i></span>
                <strong> RTW Start: </strong>
                <span class="rtw-start">{{ $student->getFormattedDate('rtw_start') }}
                  <span data-user="{{$student->id}}" class="icon is-small rtw-dates-edit">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                  </span>
                </span><br>
              </span>
              <span class="rtw-end-{{$student->id}}" @if (!$student->returned_rtw) style="display:none" @endif>
                <span class="icon is-small"><i class="fa fa-calendar-check-o" aria-hidden="true"></i></span>
                <strong> RTW End: </strong>
                <span class="rtw-end">{{ $student->getFormattedDate('rtw_end') }}
                  <span data-user="{{$student->id}}" class="icon is-small rtw-dates-edit">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                  </span>
                </span><br>
              </span>
              <span class="contract-start-{{$student->id}}" @if (!$student->has_contract) style="display:none" @endif>
                <span class="icon is-small"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                <strong> Contract Start: </strong>
                <span class="contract-start">{{ $student->getFormattedDate('contract_start') }}
                  <span data-user="{{$student->id}}" class="icon is-small contract-dates-edit">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                  </span>
                </span><br>
              </span>
              <span class="contract-end-{{$student->id}}" @if (!$student->has_contract) style="display:none" @endif>
                <span class="icon is-small"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                <strong> Contract End: </strong>
                <span class="contract-end">{{ $student->getFormattedDate('contract_end') }}
                  <span data-user="{{$student->id}}" class="icon is-small contract-dates-edit">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                  </span>
                </span><br>
              </span>
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
                        <th>Position</th>
                        <th>Course</th>
                        <th>Owner</th>
                        <th>Hours</th>
                        <th>Accepted?</th>
                        <th>Confirmed?</th>
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
                          <td>{{ $application->student_confirms ? "Yes" : "No" }}</td>
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

@endsection