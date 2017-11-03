@component('mail::message')
Dear {{$application->request->staff->forenames}},

Please be advised that one of your selected applicants ({{$application->student->fullname}}) for the role of {{$application->request->type}} on the {{$application->request->course->title}} course has failed to confirm their acceptance of the role within three working days of their selection.  This applicant has now been deselected.

Please review any remaining unselected applications as soon as possible.  If you intend to have an alternative applicant assist in teaching on your course, please toggle to select, ensuring that you select for each activity you intend the applicant to undertake.

If there are no alternative applicants at this stage you will be notified by email when there are any further applicants for the post(s).

Kind regards,

Teaching Office
@endcomponent
