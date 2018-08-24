@extends('layouts.app')

@section('content')


<div class="columns is-centered">
    <div class="column is-three-quarters">
        <h1 class="title">Upload New Courses</h1>
        <form class="import-form" enctype="multipart/form-data" method="POST" action="{{route('admin.courses.import.store')}}">
            {{ csrf_field() }}
            <div class="card">
                <div class="card-content">
                    <div class="content">
                        This page is used for uploading a spreadsheet of courses. The information we require must be in the specific columns:
                        <br><br>
                        <pre>
                            COLUMN A - CODE (i.e ENG1003)
                            COLUMN B - TITLE (i.e Analogue Electronics 1)
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