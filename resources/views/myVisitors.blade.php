@extends('layouts.layout')
@section('content')
    <div id="successdiv"></div>
@if(isset($search) || !$data->isEmpty() || isset($myVisitors))
    <div class="w-100 mw-100 container table-responsive">
        <h3>@lang('main.visitorMasterData')</h3>
        <form method="get" id="searchForm" action="@can('isgatekeeper') {{ route('gatekeeperVisitors') }} @endcan @cannot('isgatekeeper') {{ route('myVisitors') }} @endcan">
            <div class="form-row">
                <div class="col-6">
                    @cannot('isgatekeeper')
                        <input type="checkbox" id="myVisitor" name="myVisitor" @if(isset($myVisitors)) checked @endif>
                        <label for="myVisitor">@lang('main.showOnlyMyVisitors')</label>
                    @endcan
                </div>
                <div class="col-6 input-group">
                    <input name="search" @if(isset($search)) value="{{ $search }}" @endif class="form-control w-auto" type="text" placeholder="@lang('main.searchAfterSnameFnameCompany')">
                    <input name="items" value="{{ $pagitems }}" type="hidden">
                    <span>&nbsp;</span>
                    <button type="submit" class="btn btn-primary fa fa-search"></button>
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
                            <th scope="col">@sortablelink('visitorDetail', __('main.visitorDetail'))</th>
                            <th scope="col">@sortablelink('company', __('main.company'))</th>
                            <th scope="col">@sortablelink('email', __('main.email'))</th>
                            <th scope="col">@sortablelink('landlineNumber', __('main.phoneNumber'))</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!isset($search) || !empty($search))
                            @foreach($data as $items)
                                <tr id="{{ $items->id }}">
                                    <td>{{ $items->forename }}</td>
                                    <td>{{ $items->surname }}</td>
                                    <td>{{ $items->visitorCategory }}</td>
                                    <td>{{ $items->visitorDetail }}</td>
                                    <td>{{ $items->company }}</td>
                                    <td>{{ $items->email }}</td>
                                    <td>{{ $items->landlineNumber }}</td>
                                    <td nowrap="nowrap">
                                        <button type="button" data-target="#Delete" data-toggle="modal" onclick="removeVisitor({{ $items->id }})" class="btn btn-outline-danger fa-trash-alt fa"></button>
                                        <button type="button" data-target="#Edit" data-toggle="modal" onclick="editVisitor({{ $items->id }})" class="btn btn-outline-success icon-pencil align-right"></button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    <div>
                        <div class="btn-group float-right">
                            <a class="btn @if($pagitems == 5)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 5) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '5', 'search' => $search, 'myVisitor' => $myVisitors, 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">5</a>
                            <a class="btn @if($pagitems == 10)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 10) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '10', 'search' => $search, 'myVisitor' => $myVisitors, 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">10</a>
                            <a class="btn @if($pagitems == 25)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 25) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '25', 'search' => $search, 'myVisitor' => $myVisitors, 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">25</a>
                            <a class="btn @if($pagitems == 50)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 50) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '50', 'search' => $search, 'myVisitor' => $myVisitors, 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">50</a>
                        </div>
                        {{ $data->appends(['items' => $pagitems, 'search' => $search, 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->links() }}
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
                    <h2 class="text-center w-100">@lang('main.Q_DeleteVisitor')</h2>
                </div>
                <div class="modal-footer">
                    <div class="form-group col">
                        <button id="deleteVisitor" type="button" class="btn btn-outline-danger btn-full">@lang('main.delete')</button>
                    </div>
                    <div class="form-group col">
                        <button id="cancel" type="button" data-dismiss="modal" class="btn btn-outline-secondary btn-full">@lang('main.cancel')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="Edit" tabindex="-1" role="dialog" aria-labelledby="EditlLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">@lang('main.editVisitor')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('editVisitorForm')
                </div>
                <div class="modal-footer">
                    <button id="editVisitorButton" type="button" class="btn btn-primary col-12">@lang('main.save')</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script>
        $( "#myVisitor" ).click(function() {
            $( "#searchForm" ).submit();
        });
        function editVisitor(visitorId) {
            $(".is-invalid").removeClass("is-invalid");
            $(".invalid-feedback").remove();
            $.ajax({
                type: "GET",
                url: "/api/newVisitor/" + visitorId,
                dataType:"json",
                success: function(data){
                    document.getElementById("editVisitorButton").onclick = function(e)
                    {
                        if(e.button === 0) {
                            editVisitorFormSender(data["id"]);
                        }
                    };
                    $("#editvisitorCategory").val(data['visitorCategory']);
                    $("#editvisitorDetail").val(data['visitorDetail']);
                    $("#editsalutation").val(data['salutation']);
                    $("#edittitle").val(data['title']);
                    $("#editforename").val(data['forename']);
                    $("#editsurname").val(data['surname']);
                    if(data['dateOfBirth'] != "")
                    {
                        console.log(data['dateOfBirth'])
                        $("#editdateOfBirth").val(data['dateOfBirth']);
                    }
                    $("#editlanguage").val(data['language']);
                    $("#editcitizenship").val(data['citizenship']);
                    $("#editcompany").val(data['company']);
                    $("#editcompanyStreet").val(data['companyStreet']);
                    $("#editcompanyCountry").val(data['companyCountry']);
                    $("#editcompanyZipCode").val(data['companyZipCode']);
                    $("#editcompanyCity").val(data['companyCity']);
                    $("#editemail").val(data['email']);
                    $("#editlandlineNumber").val(data['landlineNumber']);
                    $("#editmobileNumber").val(data['mobileNumber']);
                    $("#editconfidentialityAgreement").val(data['confidentialityAgreement']);
                }
            });
        }
        function editVisitorFormSender(visitorId)
        {
            $.ajax({
                type: "PUT",
                url: "/api/newVisitor/" + visitorId,
                dataType:"json",
                data: {
                    visitorCategory:$("#editvisitorCategory").val(),
                    visitorDetail:$("#editvisitorDetail").val(),
                    salutation:$("#editsalutation").val(),
                    title:$("#edittitle").val(),
                    forename:$("#editforename").val(),
                    surname:$("#editsurname").val(),
                    dateOfBirth:$("#editdateOfBirth").val(),
                    language:$("#editlanguage").val(),
                    citizenship:$("#editcitizenship").val(),
                    company:$("#editcompany").val(),
                    companyStreet:$("#editcompanyStreet").val(),
                    companyCountry:$("#editcompanyCountry").val(),
                    companyZipCode:$("#editcompanyZipCode").val(),
                    companyCity:$("#editcompanyCity").val(),
                    email:$("#editemail").val(),
                    landlineNumber:$("#editlandlineNumber").val(),
                    mobileNumber:$("#editmobileNumber").val(),
                    confidentialityAgreement:$("#editconfidentialityAgreement").val(),
                    _token:"{{ csrf_token() }}",
                },
                success: function()
                {
                    $("#Edit").modal('toggle');
                    $("#alerId").remove();
                    var trest = ($('<div>').addClass("alert alert-success alert-dismissible").attr("id", "alertId").text("@lang('main.successEditVisitorMessage')"));
                    $("#successdiv").append(trest);
                    trest.append('<a aria-label="close" data-dismiss="alert" href="#" class="close">&times;</a>');
                    window.setTimeout(function() {
                        $(".alert").fadeTo(500, 0).slideUp(500, function(){
                            $(this).remove();
                        });
                    }, 10000);
                },
                error: function (data) {
                    $(".is-invalid").removeClass("is-invalid");
                    $(".invalid-feedback").remove();
                        $.each(JSON.parse(data.responseText).errors, function (key, val) {
                            $("#edit" + key).addClass("is-invalid").after("<div class='invalid-feedback'>" + val + "</div>");
                            console.log("Key: " + key.toString());
                            console.log("Val: " + val);

                        });

                }
            });
        }
        function removeVisitor(removeId) {

            $("#deleteVisitor").attr("data-dismiss", "modal");
            document.getElementById("deleteVisitor").onclick = function() {
                $.ajax({
                    type: "DELETE",
                    url: "/api/newVisitor/" + removeId,
                    dataType:"json",
                    data: {
                        deleted_at:"{{ date('Y-m-d H:i:s') }}",
                        deleted_from_id:"{{ Auth::user()->id }}",
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
