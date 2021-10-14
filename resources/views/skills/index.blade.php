@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h1>Skills Index</h1>
        </div>
        <div class="card-body">
            <table class="table table-dark">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Skill</th>
                    <th scope="col">Score</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $counter = 1;
                    @endphp
                    @foreach($skillScores as $skill => $score)
                        <tr>
                        <th scope="row">{{$counter}}</th>
                        <td>{{$skill}}</td>
                        <td>{{$score}}</td>
                        </tr>
                        @php
                            $counter++;
                    @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-body">
            <table class="table table-dark">
                <thead>
                    <tr>
                    <th scope="col">Set</th>
                    <th scope="col">Winner</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($compareResult as $set => $winner)
                        <tr>
                        <td>{{$set}}</td>
                        <td>{{$winner}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <a class="btn btn-primary" 
                href="{{ route('skills.compare', [
                    'a' => $set[0],
                    'b' => $set[1]
                ]) }}"
            >
                Start to compare
            </a>
        </div>
    </div>
@endsection