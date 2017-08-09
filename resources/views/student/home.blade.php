@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <div class="columns">
      <div class="column">
        @include('student.partials.blurb')
      </div>
      <div class="column">
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
  </div>
</div>
<br>
@include('student.partials.accepted_applications')
@foreach ($courses as $course)
  @if($course->hasRequests() and (!$course->requestsAreAllAccepted() or auth()->user()->isAcceptedOnARequest($course->id)))
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
                  @if (!$request->isFull() or auth()->user()->isAcceptedOnARequest($course->id))
                    <demonstrator-request :request="{{ $request->forVue() }}"></demonstrator-request>
                  @endif
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif
@endforeach
@endsection