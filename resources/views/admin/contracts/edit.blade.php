@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h3 class="title is-3">Toggle Students' Contract Status</h3>
    <div class="card">
      <div class="contracts-content">
        <div class="card-content">
          @foreach ($students as $student)
            <article class="media">
              <div class="media-left">
                <label class="switch">
                  <input data-user="{{ $student->id }}" class="contracts-checkbox" type="checkbox" value="1" @if ($student->has_contract) checked @endif>
                  <span class="slider round"></span>
                </label>
              </div>
              <div class="media-content">
                <div class="content">
                  <p>
                    <strong>{{ $student->fullName }}</strong>
                    <small>{{ $student->username }}</small>
                    <small>
                      <a href="mailto:{{ $student->email }}">{{ $student->email }}</a>
                    </small>
                    <small>
                        Accepted for {{ $student->totalHoursAcceptedFor() }} hours
                    </small>
                    <br>
                    {{ $student->notes }}
                  </p>
                </div>
              </div>
            </article>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endsection