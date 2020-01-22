@component('mail::message')
# Gush Profile Report

## {{ $report->file }}

Your gush profile report is ready to be downloaded, click on the button below to access your report inbox.

@component('mail::button', ['url' => url('records?download=inbox')])
Go to Report Inbox
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
