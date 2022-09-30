@extends('layout')

@section('content')
    <div class="container-lg">
        <h1 class="mt-5 mb-3">Сайты</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-nowrap" data-test="urls">
                <tbody>
                    <tr>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Последняя проверка</th>
                        <th>Код ответа</th>
                    </tr>
                    @foreach ($urls as $url)
                        <tr>
                            <td>{{ $url->id }}</td>
                            <td><a href="{{ route('urls.show', $url->id) }}">{{ $url->name }}</a></td>
                            <td>{{ $latestCheck->firstWhere('url_id', $url->id)->last_check_created_at ?? '' }}</td>
                            <td>{{ $latestCheck->firstWhere('url_id', $url->id)->status_code ?? '' }}</td>
                        </tr>
                    @endforeach
                <tbody>
            </table>

        </div>
        {{ $urls->links() }}
    </div>
@endsection
