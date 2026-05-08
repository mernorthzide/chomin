<x-mail::message>
# New CHOMIN inquiry

**Type:** {{ $inquiry->type }}

**Name:** {{ $inquiry->name }}

**Email:** {{ $inquiry->email }}

@if($inquiry->phone)
**Phone:** {{ $inquiry->phone }}
@endif

@if($inquiry->topic)
**Topic:** {{ $inquiry->topic }}
@endif

{{ $inquiry->message }}
</x-mail::message>
