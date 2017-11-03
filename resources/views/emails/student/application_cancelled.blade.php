@component('mail::message')
Dear {{$applications->first()->student->forenames}},

Following your selection by the academic staff member responsible, you have not confirmed your intention to work in the following roles.  You have now been automatically deselected.

@component('mail::table')
| Course         | Position     |
| :------------- |:-------------|
@foreach ($applications as $application)
| {{ $application->request->course->title }} | {{ $application->request->type }} |
@endforeach
@endcomponent

You can make yourself available for this course again by visiting the course entry on the Teaching Assistant home page.  Please note that this will be treated as a new application and no preference will be given on the basis of an erroneous failure to confirm.

Kind regards,

School of Engineering
@endcomponent
