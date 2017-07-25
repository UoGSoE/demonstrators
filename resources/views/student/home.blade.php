@extends('layouts.app')

@section('content')
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
                      <figure class="media-left">
                        <button class='button is-info is-outlined is-small'>Apply</button>
                      </figure>
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
              </div>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection