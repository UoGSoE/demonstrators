@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <button class="button is-pulled-right" id="info-button">Add extra information</button>
    <form class="notes-form" data-user="{{ auth()->user()->id }}" style="display:none">
      <div class="field">
        <label class="label">Extra information</label>
        <div class="control">
          <textarea name="notes" class="textarea" placeholder="Add any extra information about your availability, skills, etc.">{{ auth()->user()->notes }}</textarea>
        </div>
      </div>
      <div class="field">
        <div class="control">
          <button class="button is-success is-pulled-right submit-button">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>
@foreach ($courses as $course)
<div class="columns is-centered">
    <div class="column is-three-quarters">
        <div class="card">
            <header class="card-header">
                <p class="card-header-title">{{ $course->code }} {{ $course->title }}</p>
            </header>
            <div class="requests-content-{{$course->id}}">
              <div class="card-content">
                <div class="columns">
                  @foreach ($course->requests as $request)
                    <div class="column">
                    <article class="media">
                      <div class="media-content">
                        <div class="content">
                          <table class="table is-narrow">
                            <tr>
                              <th>Type</th>
                              <td>{{ $request->type }}</td>
                            </tr>
                            <tr>
                              <th>Academic</th>
                              <td>{{ $request->staff->fullName }}</td>
                            </tr>
                            <tr>
                              <th>Hours</th>
                              <td>{{ $request->hours_needed }}</td>
                            </tr>
                            <tr>
                              <th>Semesters</th>
                              <td>{{ $request->semester_1 }}</td>
                            </tr>
                            @if ($request->skills)
                            <tr>
                              <th>Special Requirements</th>
                              <td>{{ $request->skills }}</td>
                            </tr>
                            @endif
                          </table>
                      </div>
                    </article>
                    </div>
                  @endforeach
                </div>

                <div class="columns">
                  @foreach ($course->requests as $request)
                    <div class="column">
                      <form class="application-form" data-request="{{ $request->id }}">
                      @if ($request->hasAcceptedApplicationFrom(Auth()->user()))
                      <label class="label">You cannot change this request as it has been accepted</label>
                      @else
                      <label class="label">Please state the hours you are available</label>
                      @endif
                        <div class="field has-addons hoursapply">
                          <p class="control">
                            @if ($request->hasApplicationFrom(Auth()->user()))
                              <input name="hours" class="input" type="number" value="{{ $request->studentApplicationHours(Auth()->user()) }}">
                            @else
                              <input name="hours" class="input" type="number" value="{{ $request->hours_needed }}" required>
                            @endif
                          </p>
                          <p class="control">
                            @if ($request->hasApplicationFrom(Auth()->user()))
                              <button class="button is-success submit-button" @if ($request->hasAcceptedApplicationFrom(Auth()->user())) disabled @endif>
                                Update
                              </button>
                            @else
                              <button class="button is-info submit-button">
                                Apply
                              </button>
                            @endif
                          </p>
                        </div>
                        @if ($request->hasApplicationFrom(Auth()->user()) and !$request->hasAcceptedApplicationFrom(Auth()->user()))
                          <p class="help">Put 0 hours if you wish to withdraw</p>
                        @endif
                      </form>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection