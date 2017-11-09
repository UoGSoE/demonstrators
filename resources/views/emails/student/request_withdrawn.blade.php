@component('mail::message')
Dear {{ $forenames }},

You recently applied to work as a {{ $demonstratorRequest->type }} on the {{ $demonstratorRequest->course->title }} course.  The course coordinator has informed us that a {{ $demonstratorRequest->type }} is no longer required for this course therefore you have been withdrawn on the system.
Any other Teaching Assistant position that you have applied for and/or been accepted for remains live.
If you wish to apply for any additional positions please review the remaining opportunities on the Teaching Assistant pages.

Kind regards,

School of Engineering
@endcomponent