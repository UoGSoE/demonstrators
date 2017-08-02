@component('mail::message')
# Application Notification

Hello

You just applied for the {{ $request->type }} role for {{ $request->course->code }} {{ $request->course->title }}.

If you want to withdraw, please log back in and withdraw.

Thanks
UOGSOE

@endcomponent