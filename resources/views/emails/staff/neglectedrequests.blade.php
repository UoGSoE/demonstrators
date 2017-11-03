@component('mail::message')
Dear {{$requests->first()->staff->forenames}},

You have outstanding notifications relating to your Teaching Assistant requirements.  Please review these and take action as soon as possible.

Kind regards,

Teaching Office
@endcomponent
