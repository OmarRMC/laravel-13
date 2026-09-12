<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1f2937;
            font-size: 12px;
        }
        h1 {
            font-size: 18px;
            margin-bottom: 2px;
        }
        .subtitulo {
            color: #6b7280;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background: #f3f4f6;
            text-transform: uppercase;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <h1>{{ $evento->titulo }}</h1>
    <div class="subtitulo">
        {{ __(':count registrants', ['count' => $inscritos->count()]) }}
        &middot; {{ $evento->lugar }}
        &middot; {{ $evento->inicia_el->format('d/m/Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ __('Participant') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Code') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Attendance') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inscritos as $inscrito)
                <tr>
                    <td>{{ $inscrito->name }}</td>
                    <td>{{ $inscrito->email }}</td>
                    <td>{{ $inscrito->pivot->codigo }}</td>
                    <td>
                        @php
                            $etiquetas = [
                                'pendiente'  => __('Pending'),
                                'confirmada' => __('Confirmed'),
                                'cancelada'  => __('Cancelled'),
                            ];
                        @endphp
                        {{ $etiquetas[$inscrito->pivot->estado] ?? ucfirst($inscrito->pivot->estado) }}
                    </td>
                    <td>{{ $inscrito->pivot->asistio ? __('Yes') : __('No') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
