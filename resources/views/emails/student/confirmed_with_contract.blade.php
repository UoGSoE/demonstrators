@component('mail::message')
Dear {{$forenames}},

Thank you for confirming your intention to work as a {{$application->request->type}} on the {{$application->request->course->code}} {{$application->request->course->title}} course.  As your contract is already in place may now commence work on the course according to your schedule. Please contact the academic leading the course for more details if required.

Please retain this email as your record that you have been approved to work in this capacity.  If you have applied to work on other courses or in another role within this course, please await and then retain your confirmations for those courses/roles.

Kind regards,

School of Engineering
@endcomponent