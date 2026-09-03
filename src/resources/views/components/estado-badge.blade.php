@props(['estado'])

@php
    $colores = [
        'borrador'   => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
        'publicado'  => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'cerrado'    => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'cancelado'  => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        'pendiente'  => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'confirmada' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'cancelada'  => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    ];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium',
    $colores[$estado] ?? 'bg-gray-100 text-gray-700',
]) }}>
    {{ ucfirst($estado) }}
</span>
