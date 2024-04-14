@component('mail::message')
# Featured Subscription Expired

Hi {{ $business->name }},

We hope you enjoyed your featured subscription period with our service. We wanted to let you know that your featured subscription has expired.

If you have any questions or need assistance, feel free to contact our support team.

Thanks,<br>
The {{ config('app.name') }} Team
@endcomponent