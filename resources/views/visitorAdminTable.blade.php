@extends('layouts.layout')
@section('content')
    <div id="successdiv"></div>
    @if(isset($search) || !$data->isEmpty())
        <div class="w-100 mw-100 container table-responsive">
            <h3>@lang('main.deleteData')</h3>
            <form method="get" action="{{ route('admin.visitor.table') }}">
                <div class="form-row justify-content-end">
                    <div class="form-group col-3">
                        <input name="search" @if(isset($search)) value="{{ $search }}" @endif class="form-control h-100" type="text" placeholder="@lang('main.searchAfterSnameFnameCompany')">
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
                                <th scope="col">@sortablelink('forename', __('main.forename'))</th>
                                <th scope="col">@sortablelink('surname', __('main.surname'))</th>
                                <th scope="col">@sortablelink('visitorCategory', __('main.visitorCategory'))</th>
                                <th scope="col">@sortablelink('company', __('main.company'))</th>
                                <th scope="col">@sortablelink('email', __('main.email'))</th>
                                <th scope="col">@sortablelink('mobileNumber', __('main.mobileNumber'))</th>
                                <th scope="col">@sortablelink('landlineNumber', __('main.landlineNumber'))</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($search) && empty($search))
                            @else
                                @foreach($data as $items)
                                    <tr id="{{ $items->id }}">
                                        <td>{{ $items->forename }}</td>
                                        <td>{{ $items->surname }}</td>
                                        <td>{{ $items->visitorCategory }}</td>
                                        <td>{{ $items->company }}</td>
                                        <td>{{ $items->email }}</td>
                                        <td>{{ $items->mobileNumber }}</td>
                                        <td>{{ $items->landlineNumber }}</td>
                                        <td nowrap="nowrap">
                                            <button type="button" data-target="#Delete" data-toggle="modal" onclick="removeVisitor({{ $items->id }})" class="btn btn-outline-danger fa-trash-alt fa"></button>
                                        </td>

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
            <h2>@lang('main.noVisitors')</h2>
        @endif

            <div class="modal fade" id="Delete" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <h2 class="text-center w-100">@lang('main.Q_AdminDeleteVisitor')</h2>
                        </div>
                        <div class="modal-footer">
                            <div class="form-group col">
                                <button id="deleteVisitor" type="button" class="btn btn-outline-danger">@lang('main.delete')</button>
                            </div>
                            <div class="form-group col">
                                <button id="cancel" type="button" data-dismiss="modal" class="btn btn-outline-secondary">@lang('main.cancel')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection
@section('scripts')
    <script>
        function removeVisitor(removeId) {

            $("#deleteVisitor").attr("data-dismiss", "modal");
            document.getElementById("deleteVisitor").onclick = function() {
                $.ajax({
                    type: "POST",
                    url: "{{ route('deleteCompleteVisitor') }}",
                    data: {
                        id:removeId,
                        _token:"{{ csrf_token() }}",
                    },
                    success: function()
                    {
                        $("#" + removeId).remove();
                        $('#successdiv')
                            .append($("<div>")
                                .addClass('alert alert-success alert-dismissible')
                                .attr("id","alertId" + removeId)
                                .text("@lang('main.successDeletedVisitorMessage')")
                                .append($("<a>")
                                    .addClass("close")
                                    .attr("data-dismiss", "alert")
                                    .attr("aria-label", "close")
                                    .html("&times;")
                                ));
                        window.setTimeout(function() {
                            $("#alertId" + removeId).fadeTo(500, 0).slideUp(500, function(){
                                $(this).remove();
                            });
                        }, 10000);
                    }
                });
            };

        }
    </script>
@endsection
