@component('mail::message')
# Student confirmed Notification

Hello

The student has confirmed the {{ $application->request->type }} role for {{ $application->request->course->code }} {{ $application->request->course->title }}.

Thanks
UOGSOE

@endcomponent