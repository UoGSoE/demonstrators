@component('mail::message')
# Student declined Notification

Hello

The student has declined the {{ $application->request->type }} role for {{ $application->request->course->code }} {{ $application->request->course->title }}.

Gotta find a new one

Thanks
UOGSOE

@endcomponent