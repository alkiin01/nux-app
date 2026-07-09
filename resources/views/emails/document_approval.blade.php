@component('mail::message')
# Document Approval Required

Dear {{ $name }},

{{ $document_id }}
 
Note :

{{ $approve_msg }}

<br>

@component('mail::button', ['url' => $approval_url])
Review Document
@endcomponent
<br>

Thank you,<br>
{{ config('app.name') }}
@endcomponent
