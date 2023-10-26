@extends('layouts.layout')
@section('content')
    <div id="successdiv"></div>
    @if(isset($search) || !$data->isEmpty())
        <div class="w-100 mw-100 container table-responsive">
            <h3>@lang('main.userOverview')</h3>
            <form method="get" action="{{ route('users') }}">
                <div class="form-row justify-content-end">
                    <div class="form-group col-3">
                        <input name="search" @if(isset($search)) value="{{ $search }}" @endif class="form-control h-100" type="text" placeholder="@lang('main.searchAfterSnameFnameEmail')">
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
                                <th scope="col">@sortablelink('surname', __('main.surname'))</th>
                                <th scope="col">@sortablelink('forename', __('main.forename'))</th>
                                <th scope="col">@sortablelink('role', __('main.role'))</th>
                                <th scope="col">@sortablelink('email', __('main.email'))</th>
                                <th scope="col-1">@sortablelink('workPermit', __('main.workPermit'))</th>
                                <th scope="col-1">@sortablelink('entryPermit', __('main.entryPermit'))</th>
                                <th scope="col-1"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!isset($search) || !empty($search))
                                @foreach($data as $items)
                                    <tr id="{{ $items->id }}">
                                        <td>{{ $items->surname }}</td>
                                        <td>{{ $items->forename }}</td>
                                        <td>
                                            <select @if ($items->id == Auth::user()->id) disabled @endif name="role" id="role" onchange="setUserRole({{ $items->id }}, this.value)">
                                                @can('issuperadmin')
                                                    <option value="Super Admin" @if ($items->role == "Super Admin")
                                                        selected
                                                    @endif>@lang('main.superAdmin')</option>
                                                    <option value="Admin" @if ($items->role == "Admin")
                                                        selected
                                                    @endif>@lang('main.admin')</option>
                                                @endcan
                                                <option value="Gatekeeper" @if ($items->role == "Gatekeeper")
                                                    selected
                                                @endif>@lang('main.gatekeeper')</option>
                                                <option value="Employee" @if ($items->role == "Employee")
                                                    selected
                                                @endif>@lang('main.employee')</option>
                                            </select>
                                        </td>
                                        <td>{{ $items->email }}</td>
                                        <td><input type="checkbox" id="workPermit" onclick="setUserWorkPermit({{ $items->id }}, this.checked)" name="workPermit" @if ($items->canIssueWorkPermit == 1) checked="checked" @endif></td>
                                        <td><input type="checkbox" id="entryPermit" onclick="setUserEntryPermit({{ $items->id }}, this.checked)" name="entryPermit" @if ($items->canIssueEntryPermit == 1) checked="checked" @endif></td>
                                        <td nowrap="nowrap">
                                            <button type="button" data-target="#Delete" data-toggle="modal" onclick="removeUser({{ $items->id }})" class="btn btn-outline-danger fa-trash-alt fa"></button>
                                        </td>
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
            <h2>@lang('main.noUsers')</h2>
        @endif


            <div class="modal fade" id="Delete" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <h2 class="text-center w-100">@lang('main.Q_AdminDeleteUser')</h2>
                        </div>
                        <div class="modal-footer">
                            <div class="form-group col">
                                <button id="deleteUser" type="button" class="btn btn-outline-danger">@lang('main.delete')</button>
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
        function setUserWorkPermit(Id, setPermit)
        {
            $.ajax({
                    type: "POST",
                    url: "{{ route('setUserWorkPermit') }}",
                    data: {
                        id:Id,
                        canIssueWorkPermit:setPermit,
                        _token:"{{ csrf_token() }}",
                    }
                });
        }
        function setUserEntryPermit(Id, setPermit)
        {
            $.ajax({
                    type: "POST",
                    url: "{{ route('setUserEntryPermit') }}",
                    data: {
                        id:Id,
                        canIssueEntryPermit:setPermit,
                        _token:"{{ csrf_token() }}",
                    }
                });
        }

        function setUserRole(Id, value)
        {
            $.ajax({
                    type: "POST",
                    url: "{{ route('setUserRole') }}",
                    data: {
                        id:Id,
                        role:value,
                        _token:"{{ csrf_token() }}",
                    }
                });
        }
    </script>


<script>
    function removeUser(removeId) {

        $("#deleteUser").attr("data-dismiss", "modal");
        document.getElementById("deleteUser").onclick = function() {
            $.ajax({
                type: "POST",
                url: "{{ route('deleteUser') }}",
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
                            .text("@lang('main.successDeletedUserMessage')")
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
