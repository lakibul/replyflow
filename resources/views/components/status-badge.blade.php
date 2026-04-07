@props(['status'])

@php
$map = [
    'pending'    => 'bg-yellow-100 text-yellow-800',
    'processing' => 'bg-blue-100 text-blue-800',
    'completed'  => 'bg-green-100 text-green-800',
    'failed'     => 'bg-red-100 text-red-800',
];
$class = $map[$status] ?? 'bg-gray-100 text-gray-800';
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $class }}">
    {{ ucfirst($status) }}
</span>
