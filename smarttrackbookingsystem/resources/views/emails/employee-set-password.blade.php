@component('mail::message')
# Hi {{ $user->name ?? 'there' }},

Your employee account has been created on **{{ config('app.name') }}**.

Please set your password using the button below:

@component('mail::button', ['url' => $url])
Set Password
@endcomponent

This link will expire soon. If you didn’t request this, you can safely ignore this email.

Thanks,  
**{{ config('app.name') }}**
@endcomponent