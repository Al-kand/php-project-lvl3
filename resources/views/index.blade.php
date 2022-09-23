@extends('layout')

@section('content')
    <div class="container-lg">
        <h1 class="mt-5 mb-3">Сайты</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-nowrap">
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Последняя проверка</th>
                    <th>Код ответа</th>
                </tr>
                @foreach ($urls as $url)
                    <tr>
                        <th>{{ $url->id }}</th>
                        <th><a href="{{route('urls.show', $url->id)}}">{{ $url->name }}</a></th>
                        <th>{{ $latestCheck->firstWhere('url_id', $url->id)->status_code ?? ''}}</th>
                        <th>{{ $latestCheck->firstWhere('url_id', $url->id)->last_check_created_at ?? '' }}</th>
                    </tr>
                @endforeach
            </table>
            
        </div>
        {{ $urls->links() }}
    </div>
@endsection
