@component('mail::message')
# Subscription Expired

Hi {{ $business->name }},

We hope you enjoyed your subscription period with our service. We wanted to let you know that your subscription has expired. To continue using our service,

We offer other various subscription plans that provide additional features and benefits.

If you have any questions or need assistance, feel free to contact our support team.

Thanks,<br>
The {{ config('app.name') }} Team
@endcomponent