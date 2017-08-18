@if (auth()->user()->acceptedApplications()->isNotEmpty())
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <div class="card">
      <header class="card-header">
        <p class="card-header-title">Accepted Applications</p>
      </header>
      <div class="card-content">
        You have been accepted for a position. Please confirm if you are still able to do this position, or decline if not.
        <br><br>
        <table class="table is-narrow is-striped">
          <tbody>
            @foreach(auth()->user()->acceptedApplications() as $application)
              <tr class="row-{{ $application->id }}">
                <td>{{ $application->request->course->code }} {{ $application->request->course->title}}</td>
                <td>{{ $application->request->type }}</td>
                <td>{{ $application->request->hours_needed }} hours</td>
                <td style="width:50%">
                  <span class="is-pulled-right">
                    <a data-application="{{ $application->id }}" class="button is-success is-small accept-position">
                      <span class="icon is-small">
                        <i class="fa fa-check"></i>
                      </span>
                      <span>Accept</span>
                    </a>
                    <a data-application="{{ $application->id }}" class="button is-danger is-small decline-position">
                      <span class="icon is-small">
                        <i class="fa fa-times"></i>
                      </span>
                      <span>Decline</span>
                    </a>
                  </span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endif