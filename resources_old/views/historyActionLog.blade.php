@extends('layouts.layout')
@section('content')
    <div id="successdiv"></div>
    @if(isset($search) || !$data->isEmpty())
        <div class="w-100 mw-100 container table-responsive">
            <h3>@lang('main.actionHistoryLog')</h3>
            <form method="get" action="{{ route('action.history.log') }}">
                <div class="form-row justify-content-end">
                    <div class="form-group col-3">
                        <input name="search" @if(isset($search)) value="{{ $search }}" @endif class="form-control h-100" type="text" placeholder="@lang('main.searchAfterUser')">
                    </div>
                    <input name="items" value="{{ $pagitems }}" type="hidden">
                    <div class="form-group col-auto">
                        <button type="submit" class="btn form-control btn-primary fa fa-search"></button>
                    </div>
                </div>
            </form>

            <div><hr class="invisible"></div>
            <div>
                <div class="row">
                    <div class="col">
                        <table class="table table-hover table-striped">
                            <thead @if(env("APP_table_Color")) class="table-dark" style="background: {{ env("APP_table_Color") }}" @else class="thead-dark" @endif>
                            <tr>
                                <th scope="col">@sortablelink('User', __('main.users'))</th>
                                <th scope="col">@sortablelink('action', __('main.action'))</th>
                                <th scope="col">@sortablelink('date', __('main.date'))</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!isset($search) || !empty($search))
                                @foreach($data as $items)
                                    <tr id="{{ $items->id }}">
                                        <td>{{ $items->User }}</td>
                                        <td>{{ $items->action }}</td>
                                        <td>{{ date('d.m.Y H:i', strtotime($items->date)) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        <div>
                            <div class="btn-group float-right">
                                <a class="btn @if($pagitems == 5)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 5) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '5', 'search' => $search])->url(1) }}">5</a>
                                <a class="btn @if($pagitems == 10)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 10) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '10', 'search' => $search])->url(1) }}">10</a>
                                <a class="btn @if($pagitems == 25)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 25) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '25', 'search' => $search])->url(1) }}">25</a>
                                <a class="btn @if($pagitems == 50)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 50) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '50', 'search' => $search])->url(1) }}">50</a>
                            </div>
                            {{ $data->appends(['items' => $pagitems, 'search' => $search])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
            <h2>@lang('main.noHistory')</h2>
        @endif

@endsection
@section('scripts')
    <script>

    </script>

@endsection
