@component('mail::message')
Dear {{ $application->student->forenames }},

We are pleased to inform you that you have been selected to work as a {{ $application->request->type }} on the {{ $application->request->course->code }} {{ $application->request->course->title }} course.

Please indicate your acceptance of this position by loggin in to the School Teaching Assistant Pages and clicking the 'Accept' button in the course entry. You have a maximum of 3 working days to complete your acceptance of the position otherwise it will be assumed you do not wish to take up the position. If applicable, please ensure that you confirm all of your intended teaching activities.

Once you have accepted your position you will receive an email detailing the steps necessary to complete your contract prior to commencing work.

Kind regards,

School of Engineering
@endcomponent