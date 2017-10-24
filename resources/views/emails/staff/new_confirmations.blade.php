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

Please review your new applications as soon as possible.  If you would like the applicants to assist in teaching on your course, please toggle to select, ensuring that you select for each activity you intend the applicant to undertake.  If you do not intend to accept the applicant, you need not take any action in relation to that specific applicant.

For declined positions, please review any remaining unselected applications as soon as possible.  If you intend to have an alternative applicant assist in teaching on your course, please toggle to select, ensuring that you select for each activity you intend the applicant to undertake.

Kind regards,

Teaching Office
@endcomponent