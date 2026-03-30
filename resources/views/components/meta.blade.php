@props(['title' => 'CHOMIN', 'description' => '', 'image' => ''])

<title>{{ $title }} | CHOMIN</title>
<meta name="description" content="{{ $description }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
@if($image)
<meta property="og:image" content="{{ $image }}">
@endif
