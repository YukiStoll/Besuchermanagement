@extends('layouts.layout')
@section('content')
    <div id="successdiv"></div>
    @if(isset($search) || !$data->isEmpty())
        <div class="w-100 mw-100 container table-responsive">
            <h3>@lang('main.overviewBadges')</h3>
            <form method="get" action="{{ route('badgeOverview') }}">
                <div class="form-row justify-content-end">
                    <div class="form-group col-3">
                        <input name="search" @if(isset($search)) value="{{ $search }}" @endif class="form-control h-100" type="text" placeholder="@lang('main.searchAfterBadge')">
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
                                <th scope="col">@sortablelink('cardID', __('main.cardID'))</th>
                                <th scope="col">@sortablelink('firstName', __('main.surname'))</th>
                                <th scope="col">@sortablelink('lastName', __('main.forename'))</th>
                                <th scope="col">@sortablelink('type', __('main.mawaType'))</th>
                                <th scope="col">@sortablelink('validFrom', __('main.validFrom'))</th>
                                <th scope="col">@sortablelink('validTo', __('main.validTo'))</th>
                                <th scope="col-1">@sortablelink('doors', __('main.doors'))</th>
                                <th scope="col-1">@sortablelink('createdThroughSystem', __('main.createdThroughSystem'))</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!isset($search) || !empty($search))
                                @foreach($data as $items)
                                    <tr id="{{ $items->cardID }}" table="{{ $items->createdThroughSystem }}">
                                        <td>{{ $items->cardID }}</td>
                                        <td>{{ $items->firstName }}</td>
                                        <td>{{ $items->lastName }}</td>
                                        <td>{{ $items->type }}</td>
                                        <td>{{ date('d.m.Y', strtotime($items->validFrom)) }}</td>
                                        <td class="
                                        @if ($items->validTo == now()->startOfDay())
                                            text-warning
                                        @elseif ($items->validTo < now()->startOfDay())
                                            text-danger
                                        @else
                                            text-success
                                        @endif">{{ date('d.m.Y', strtotime($items->validTo)) }}</td>
                                        <td>{{ $items->doors }}</td>
                                        <td>@if ($items->createdThroughSystem == 'true')
                                            <a class="text-primary" href="{{ route('Visits', 'id=' . $items->visitID) }}">{{__('main.createdThroughSystem_' . $items->createdThroughSystem)}}</a>
                                            @else
                                            {{__('main.createdThroughSystem_' . $items->createdThroughSystem)}}
                                        @endif</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        <div>
                            <div class="btn-group float-right">
                                <a class="btn @if($pagitems == 5)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 5) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '5', 'search' => $search, 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">5</a>
                                <a class="btn @if($pagitems == 10)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 10) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '10', 'search' => $search, 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">10</a>
                                <a class="btn @if($pagitems == 25)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 25) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '25', 'search' => $search, 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">25</a>
                                <a class="btn @if($pagitems == 50)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 50) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '50', 'search' => $search, 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">50</a>
                            </div>
                            {{ $data->appends(['items' => $pagitems, 'search' => $search, 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
            <h2>@lang('main.noBadges')</h2>
        @endif
@endsection
