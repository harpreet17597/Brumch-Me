@component('mail::message')

Hi {{ env('MAIL_FROM_NAME') }},

# {{$query->title}}

{{$query->query}}

Thanks,<br>
{{ $query->user->name }} <br>
{{ $query->user->email }} <br>
{{ $query->user->phone_country }} {{ $query->user->phone }}
@endcomponent