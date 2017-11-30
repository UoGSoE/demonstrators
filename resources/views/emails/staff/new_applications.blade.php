@component('mail::message')
Dear {{ $academic }},

Please be advised that you have new applications for your requests.

@component('mail::table')
| Student       | Course         | Position  |
| :------------- |:-------------|:--------|
@foreach ($applications as $application)
| {{ $application->student->fullName }} | {{ $application->request->course->title }} | {{ $application->request->type }} |
@endforeach
@endcomponent

Please review your new application as soon as possible here {{ url('/') }}.  If you would like the applicant to assist in teaching on your course, please log in to the system, click on the 'Applicants' tab and toggle to select, ensuring that you select for each activity you intend the applicant to undertake.  If you do not intend to accept the applicant, you need not take any action in relation to that specific applicant.

Kind regards,

Teaching Office
@endcomponent