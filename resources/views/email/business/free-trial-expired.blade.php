@component('mail::message')
# Free Trial Expired

Hi {{ $business->name }},

We hope you enjoyed your free trial period with our service. We wanted to let you know that your free trial has expired. To continue using our service, please upgrade to a paid subscription.

We offer various subscription plans that provide additional features and benefits.

If you have any questions or need assistance, feel free to contact our support team.

Thanks,<br>
The {{ config('app.name') }} Team
@endcomponent