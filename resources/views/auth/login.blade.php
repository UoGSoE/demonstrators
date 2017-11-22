@extends('layouts.app')

@section('content')
<div class="columns is-centered">
<div class="column is-one-quarter">
<div class="card">
    <header class="card-header">
        <p class="card-header-title">
            Login
        </p>
    </header>
    <form id="login-form" method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}
        <div class="card-content">
            <div class="content">
                <div class="field">
                    <p class="control has-icons-left has-icons-right">
                        <input name="username" class="input" type="text" placeholder="GUID" value="{{ old('username') }}" required autofocus>
                        <span class="icon is-small is-left">
                            <i class="fa fa-envelope"></i>
                        </span>
                    </p>
                    @if ($errors->has('username'))
                        <p class="help">
                            <strong>{{ $errors->first('username') }}</strong>
                        </p>
                    @endif
                </div>

                <div class="field">
                  <p class="control has-icons-left">
                    <input name="password" class="input" type="password" placeholder="Password" required>
                    <span class="icon is-small is-left">
                      <i class="fa fa-lock"></i>
                    </span>
                  </p>
                  @if ($errors->has('password'))
                      <p class="help">
                          <strong>{{ $errors->first('password') }}</strong>
                      </p>
                  @endif
                </div>
            </div>
        </div>
        <footer class="card-footer">
            <button id="login" class="button is-primary card-footer-item">Login</button>
        </footer>
    </form>
</div>
</div>
</div>
@endsection
