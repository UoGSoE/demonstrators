@if (auth()->user()->acceptedUnconfirmedApplications()->isNotEmpty())
<div class="columns is-centered">
    <div class="column is-three-quarters">
      <student-positions :applications='@json(auth()->user()->acceptedUnconfirmedApplications())'></student-positions>
    </div>
</div>
@endif