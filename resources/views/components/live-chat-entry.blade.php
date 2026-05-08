@php
    $lineChatUrl = \App\Models\SiteSetting::get('line_chat_url');
@endphp

@if($lineChatUrl)
    <a href="{{ $lineChatUrl }}"
       target="_blank"
       rel="noopener"
       class="fixed bottom-5 right-5 z-[60] inline-flex h-12 w-12 items-center justify-center bg-brand-black text-white shadow-lg hover:bg-brand-gray-dark focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2"
       aria-label="Chat with CHOMIN on LINE">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm3.75 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm3.75 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12c0 4.556-4.03 8.25-9 8.25a9.77 9.77 0 0 1-2.555-.337L3 21l1.69-4.225A7.69 7.69 0 0 1 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
        </svg>
    </a>
@endif
