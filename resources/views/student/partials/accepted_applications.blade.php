@if (auth()->user()->acceptedApplications()->isNotEmpty())
<div class="columns is-centered">
    <div class="column is-three-quarters">
      <student-positions :applications='@json(auth()->user()->acceptedApplications())'></student-positions>
    </div>
</div>

@endif