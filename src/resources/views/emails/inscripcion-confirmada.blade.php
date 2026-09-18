<h1>Inscripcion confirmada</h1>
<p>Te inscribiste en <strong>{{ $evento->titulo }}</strong>.</p>
<ul>
    <li>Fecha: {{ $evento->inicia_el->format('d/m/Y H:i') }}</li>
    <li>Lugar: {{ $evento->lugar }}</li>
    <li>Tu codigo: <strong>{{ $codigo }}</strong></li>
</ul>
