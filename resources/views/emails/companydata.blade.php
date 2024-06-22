<x-mail::message>
# {{ $companyName }}

From {{ $startDate }} to {{ $endDate }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
