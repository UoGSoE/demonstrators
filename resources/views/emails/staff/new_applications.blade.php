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

Please review your new applications as soon as possible. If you intend to have the applicant assist in teaching on your course, please toggle to select, ensuring that you select for each activity you intend the applicant to undertake. If you do not intend to accept the applicant, you need not take any action in relation to that specific applicant.

Kind regards,

Teaching Office

@endcomponent