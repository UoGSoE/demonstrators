@component('mail::message')
# Request withdrawn

Hello

The {{ $demonstratorRequest->type }} role for {{ $demonstratorRequest->course->code }} {{ $demonstratorRequest->course->title }} has been withdrawn.

Sorry

Try something else

Thanks
UOGSOE

@endcomponent