<h1>Skills Index</h1>

@if (!empty($compareResult))
    <h3>Compare result</h3>
    {!! var_dump($compareResult) !!}
@else 
    <h3>No result</h3>
@endif

<div class="row">
    <a href="{{ route(
        'skills.compare',
        [
            'a' => $set[0],
            'b' => $set[1]
        ]
    ) }}">
        Start to compare
    </a>
</div>