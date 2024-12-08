@component('mail::message')

**{{ $user->name }}** has invited you to work on their NearMint account.
Please accept this invite to create your account and get started.

@component('mail::button', ['url' => $invite_link])
    Accept Invitation
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
