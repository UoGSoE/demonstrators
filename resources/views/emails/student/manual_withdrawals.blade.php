@component('mail::message')
Dear {{$forenames}}, 

You have now withdrawn from consideration for the following roles.

@foreach ($applications as $application)
{{ $application->request->type }} {{ $application->request->course->fullTitle }}
@endforeach

Thank you for notifying the School of your decision.

If you withdrew in error, you can make yourself available again by visiting the course entry on the Teaching Assistant home page.  Please note that this will be treated as a new application and no preference will be given on the basis of an erroneous withdrawal.

Kind regards,

School of Engineering
@endcomponent