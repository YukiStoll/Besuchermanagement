@extends('layouts.layout')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <table class="table table-hover table-striped">
                <thead @if(env("APP_table_Color")) class="table-dark" style="background: {{ env("APP_table_Color") }}" @else class="thead-dark" @endif>
                <tr>
                    <th scope="col">@sortablelink('name', __('main.name'))</th>
                    <th style="width: 10.00%"></th>
                </tr>
                </thead>
                <tbody>
                @if(isset($data) && empty($data))
                @else
                    @foreach($data as $items)
                        <tr id="{{ $items->id }}">
                            <td>@lang('main.' . $items->name)</td>
                            <td class="text-right" nowrap="nowrap">
                                <a type="button" href="{{ route("emailTemplates", ['id' => $items->id, 'language' => 'german']) }}" class="btn btn-outline-success text-success icon-pencil align-right"></a>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>

            <div>
                <div class="btn-group float-right">
                    <a class="btn @if($pagitems == 5) btn-dark @else btn-light @endif btn-sm" @if($pagitems == 5) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '5', 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">5</a>
                    <a class="btn @if($pagitems == 10) btn-dark @else btn-light @endif btn-sm" @if($pagitems == 10) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '10', 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">10</a>
                    <a class="btn @if($pagitems == 25) btn-dark @else btn-light @endif btn-sm" @if($pagitems == 25) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '25', 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">25</a>
                    <a class="btn @if($pagitems == 50) btn-dark @else btn-light @endif btn-sm" @if($pagitems == 50) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '50', 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">50</a>
                </div>
                {{ $data->appends(
                    [
                        'items' => $pagitems,
                        'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "",
                        'direction' => isset($_GET["direction"]) ? $_GET["direction"] : "",
                    ]
                )->links() }}
            </div>
        </div>
    </div>
</div>


@endsection
@section('scripts')
<script>

</script>
@endsection
