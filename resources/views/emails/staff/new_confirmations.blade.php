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

For declined positions, please review any remaining unselected applications as soon as possible.  If you intend to have an alternative applicant assist in teaching on your course, please toggle to select, ensuring that you select for each activity you intend the applicant to undertake.

Kind regards,

Teaching Office
@endcomponent