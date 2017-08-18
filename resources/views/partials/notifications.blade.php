@if(Session::has('success_message') or count($errors) > 0)
    <br>
    <div class="columns is-centered">
        <div class="column is-three-quarters">
            @if(Session::has('success_message'))
                <div class="notification is-success">
                    {{ Session::get('success_message') }}
                </div>
            @endif
            @foreach($errors->all() as $error)
                <div class="notification is-danger">
                    {{ $error }}
                </div>
            @endforeach
        </div>
    </div>
@endif