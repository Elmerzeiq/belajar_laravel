{{-- resources/views/components/sidebar/links.blade.php --}}
@props(['title', 'route', 'icon'])

@php
    // Agar aktif di halaman utama dan sub-route (misal index, create, edit)
    $active = request()->routeIs($route . '*') ? 'active' : '';
@endphp

<li class="pc-item">
    <a href="{{ route($route) }}" class="pc-link {{ $active }}">
        <span class="pc-micon"><i class="{{ $icon }}"></i></span>
        <span class="pc-mtext">{{ $title }}</span>
    </a>
</li>
