@component('mail::message')

Dear {{ $academic }},

Please be advised that students have confirmed/declined their proposed positions.

@component('mail::table')
| Student       | Course         | Position  |         |
| :------------- |:-------------|:--------|:--------|
@foreach ($applications as $application)
| {{ $application->student->fullName }} | {{ $application->request->course->title }} | {{ $application->request->type }} | {{ $application->student_confirms ? 'Confirmed' : 'Declined' }} |
@endforeach
@endcomponent

Kind regards,

Teaching Office

@endcomponent