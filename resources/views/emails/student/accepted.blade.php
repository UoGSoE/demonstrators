@component('mail::message')
# Accepted Notification

Hello

You have been accepted for the {{ $application->request->type }} role for {{ $application->request->course->code }} {{ $application->request->course->title }}.

Please confirm you can still do this.

YES
NO

Thanks
UOGSOE

@endcomponent