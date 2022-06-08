@extends('layouts.app')

@section('content')
    <div class="container-lg">
        <h1 class="mt-5 mb-3">Сайты</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-nowrap">
                <tr>
                    @foreach ($urls as $url)
                        <th>{{ $url->id }}</th>
                        <th>{{ $url >name }}</th>
                    @endforeach

                </tr>
            </table>
        </div>
    </div>
@endsection
