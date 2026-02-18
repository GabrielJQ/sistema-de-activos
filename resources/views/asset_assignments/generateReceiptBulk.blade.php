@foreach($groupedByTag as $tag => $assignments)
    <h3>Resguardo: {{ $tag }}</h3>
    @foreach($assignments as $assignment)
        <p>{{ $assignment->asset->deviceType->equipo ?? '-' }} - {{ $assignment->asset->serie ?? '-' }}</p>
    @endforeach
    <div class="page-break"></div>
@endforeach
