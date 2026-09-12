<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1f2937;
            text-align: center;
            padding-top: 60px;
        }
        .marco {
            border: 4px solid #4338ca;
            padding: 50px 60px;
        }
        .titulo {
            font-size: 28px;
            font-weight: bold;
            color: #4338ca;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 30px;
        }
        .texto {
            font-size: 14px;
            color: #4b5563;
        }
        .nombre {
            font-size: 26px;
            font-weight: bold;
            margin: 20px 0;
            color: #111827;
        }
        .evento {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0 20px;
        }
        .detalle {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 40px;
        }
        .codigo {
            font-size: 11px;
            color: #9ca3af;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="marco">
        <div class="titulo">{{ __('Certificate of Participation') }}</div>

        <div class="texto">{{ __('This certifies that') }}</div>
        <div class="nombre">{{ $participante->name }}</div>

        <div class="texto">{{ __('participated in the event') }}</div>
        <div class="evento">{{ $evento->titulo }}</div>

        <div class="detalle">
            {{ __(':date at :place', ['date' => $evento->inicia_el->format('d/m/Y'), 'place' => $evento->lugar]) }}
            <br>
            {{ __('Organized by :name', ['name' => $evento->organizador->name]) }}
        </div>

        <div class="codigo">{{ __('Code') }}: {{ $codigo }}</div>
    </div>
</body>
</html>
