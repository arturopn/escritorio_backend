@component('mail::message')
# Hello {{$user->name}}

Youn changed your email, so we need to verify this new address. Please use the link below:

@component('mail::button', ['url' => route('verify', $user->verification_token)])
Verify Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
