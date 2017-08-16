@component('mail::message')
# Manual withdrawals

Hello

You were withdrawn from the following job requests

@foreach ($applications as $application)
{{ $application->request->type }} {{ $application->request->course->fullTitle }}
@endforeach

If this is wrong then tell us

Thanks
UOGSOE

@endcomponent