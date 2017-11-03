@extends('layouts.app')

@section('content')


<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h1 class="title">Import Requests</h1>
    <form class="import-form" enctype="multipart/form-data" method="POST" action="{{route('import.update')}}">
      {{ csrf_field() }}
      <div class="card">
        <div class="card-content">
          <div class="content">
            This page is used for uploading a spreadsheet of the current courses, their academics and their required requests for demonstrators, tutors or markers. The information we require must be in the specific columns:
            <br><br>
            <pre>
              COLUMN A - SUBJECT (i.e ENG)
              COLUMN B - CAT (i.e 1003)
              COLUMN C - LONG TITLE (i.e Analogue Electronics 1)
              COLUMN D - ASSOCIATED ACADEMIC (GUID)
              COLUMN E - NO. DEMONSTRATORS (i.e 5)
              COLUMN F - HOURS/DEMONSTRATOR (i.e 10)
              COLUMN H - NO. TUTORS
              COLUMN I - HOURS/TUTOR
              COLUMN K - NO. MARKERS
              COLUMN L - HOURS/MARKER
              COLUMN T - SPECIAL REQUIREMENTS (i.e Matlab Programming)
              COLUMN U - SEMESTER (i.e 1 & 2)
            </pre>
            <input type="file" name="spreadsheet" required>
          </div>
        </div>
        <footer class="card-footer">
          <button class="button is-primary card-footer-item import-button">Import</button>
        </footer>
      </div>
    </form>
  </div>
</div>
@endsection