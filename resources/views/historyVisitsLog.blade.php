@extends('layouts.layout')
@section('content')
    <div id="successdiv"></div>
        <div class="w-100 mw-100 container table-responsive">
            <h3>@lang('main.visitsHistory')</h3>
            <form method="get" id="searchForm" action="{{ route('visits.history.log') }}">
                <div class="form-row">
                    <div class="justify-content-start col-4">
                        <div class="form-row">
                            <div class="form-group col-auto">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="activeVisits" name="activeVisits" @if(isset($requestData['activeVisits'])) checked @endif>
                                    <label class="form-check-label" for="activeVisits">@lang('main.ShowOnlyVisitsThatAreCurrentlyTakingPlace')</label>
                                </div>
                            </div>

                            <div class="form-group col">
                                <select class="form-control" id="visitorDetail" name="visitorDetail" autofocus>
                                    <option @if(!isset($requestData['visitorDetail'])) selected @endif value=""></option>
                                    <option @if(isset($requestData['visitorDetail']) && $requestData['visitorDetail'] == "-") selected @endif value="-">-</option>
                                    <option @if(isset($requestData['visitorDetail']) && $requestData['visitorDetail'] == "con1") selected @endif value="con1">Con1</option>
                                    <option @if(isset($requestData['visitorDetail']) && $requestData['visitorDetail'] == "con2") selected @endif value="con2">Con2</option>
                                </select>
                                @error('visitorDetail')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="justify-content-end col">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="col-form-label-lg">@lang('main.from')</label>
                            </div>

                            <div class="form-group col-auto">
                                <input id="von" name="von" @isset($requestData['startDate']) value="{{ $requestData['startDate'] }}" @endisset class="form-control form-inline" type="date">
                            </div>

                            <div class="form-group">
                                <label class="col-form-label-lg">@lang('main.to')</label>
                            </div>

                            <div class="form-group col-auto">
                                <input id="bis" name="bis" @isset($requestData['endDate']) value="{{ $requestData['endDate'] }}" @endisset class="form-control" type="date">
                            </div>

                            <div class="form-group col-5">
                                <input id="search" name="search" @isset($requestData['search']) value="{{ $requestData['search'] }}" @endisset class="form-control" type="text" placeholder="@lang('main.searchAfterSnameFnameCompany')">
                            </div>
                            <input name="items" value="{{ $pagitems }}" type="hidden">
                            <div class="form-group col-auto">
                                <button type="submit" class="btn form-control btn-primary fa fa-search"></button>
                            </div>
                            <div class="form-group col-auto">
                                <button type="button" onclick="getCSV()" class="btn form-control btn-success fa fa-file-export"></button>
                            </div>


                        </div>
                    </div>
                </div>
            </form>

            <div><hr class="invisible"></div>
            <div>
                <div class="row">
                    <div class="col">
                        <table class="table table-small-padding table-hover table-striped">
                            <thead @if(env("APP_table_Color")) class="table-dark" style="background: {{ env("APP_table_Color") }}" @else class="thead-dark" @endif>
                            <tr>
                                <th scope="col">@sortablelink('startDate', __('main.startDateTable'))</th>
                                <th scope="col">@sortablelink('endDate', __('main.endDateTable'))</th>
                                <th scope="col">@sortablelink('Company', __('main.company'))</th>
                                <th scope="col">@sortablelink('Visitor', __('main.visitor'))</th>
                                <th scope="col">@sortablelink('visitorCategory', __('main.visitorCategoryTable'))</th>
                                <th scope="col">@sortablelink('visitorDetail', __('main.visitorDetail'))</th>
                                <th scope="col">@sortablelink('name', __('main.employee'))</th>
                                <th scope="col">@sortablelink('visitId', __('main.visitId'))</th>
                                <th scope="col">@sortablelink('created_at', __('main.createdAt'))</th>
                                <th scope="col">@sortablelink('updated_at', __('main.updatedAt'))</th>
                                <th scope="col">@sortablelink('itemCatagory', __('main.catagory'))</th>
                                <th scope="col">{{__('main.presenceTime')}}</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!isset($search) || !empty($search))
                                @foreach($data as $items)
                                    <tr id="{{ $items->id }}">
                                        <td>{{ date('d.m.Y H:i', strtotime($items->startDate)) }}</td>
                                        <td>{{ date('d.m.Y H:i', strtotime($items->endDate)) }}</td>
                                        <td>{{ $items->Company }}</td>
                                        <td>{{ $items->Visitor }}</td>
                                        <td>{{ $items->visitorCategory }} @if ($items->party == 1) (@lang("main.group")) @endif</td>
                                        <td>{{ $items->visitorDetail }}</td>
                                        <td>{{ $items->name }}</td>
                                        <td>{{ $items->visitId }}</td>
                                        <td>{{ date('d.m.Y H:i', strtotime($items->created_at)) }}</td>
                                        <td>{{ date('d.m.Y H:i', strtotime($items->updated_at)) }}</td>
                                        <td>{{ __('main.' . $items->itemCatagory) }}</td>
                                        <td>{{ $items->presenceTime }}</td>
                                        <td>
                                            <button type="button" data-target="#View" data-toggle="modal" onclick="viewVisit({{ $items->id }}, '{{ $items->itemCatagory }}')" class="btn btn-outline-success icon- align-right"><i class="fas fa-search-plus"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        <div>
                            <div class="btn-group float-right">
                                <a class="btn @if($pagitems == 5)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 5) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '5', 'search' => $search, 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : "", 'activeVisits' => isset($_GET["activeVisits"]) ? $_GET["activeVisits"] : "",])->url(1) }}">5</a>
                                <a class="btn @if($pagitems == 10)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 10) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '10', 'search' => $search, 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : "", 'activeVisits' => isset($_GET["activeVisits"]) ? $_GET["activeVisits"] : "",])->url(1) }}">10</a>
                                <a class="btn @if($pagitems == 25)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 25) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '25', 'search' => $search, 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : "", 'activeVisits' => isset($_GET["activeVisits"]) ? $_GET["activeVisits"] : "",])->url(1) }}">25</a>
                                <a class="btn @if($pagitems == 50)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 50) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '50', 'search' => $search, 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : "", 'activeVisits' => isset($_GET["activeVisits"]) ? $_GET["activeVisits"] : "",])->url(1) }}">50</a>
                            </div>
                            {{ $data->appends(['items' => $pagitems, 'search' => $search, 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : "", 'activeVisits' => isset($_GET["activeVisits"]) ? $_GET["activeVisits"] : "",])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="View" tabindex="-1" role="dialog" aria-labelledby="ViewlLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">@lang('main.advancedRegistration')</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @include('viewAdvanceRegistrationForm')
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        $( "#activeVisits" ).click(function() {
            $( "#searchForm" ).submit();
        });
        function viewVisit(id, type)
        {
            if(type == "visit")
            {
                $.ajax({
                    type: "GET",
                    url: "/api/newVisit/" + id,
                    dataType:"json",
                    success: function(data){
                        fillVisitView(data);
                    }
                })
            }
            else
            {
                $.ajax({
                    type: "GET",
                    url: "/api/newAdvancedRegistration/" + id,
                    dataType:"json",
                    success: function(data){
                        fillVisitView(data);
                    }
                })
            }


        }

        function fillVisitView(visit)
        {
            $("#personToVisitLable").addClass("d-none");
            console.log(visit['startDate']);
            $('#startDate').val(visit['startDate']);
            $("#startTime").val(visit['startTime']);
            $("#endDate").val(visit['endDate']);
            $("#endTime").val(visit['endTime']);
            console.log('=======================================================');
            console.log(visit);
            console.log('roadmap: 0 == ' + visit['roadmap']);
            console.log('hygieneRegulations: 1 == ' + visit['hygieneRegulations']);
            if(visit['roadmap'] === 0)
            {
                $("#roadmap0").prop('checked', 'checked');
                $("#roadmap0").removeAttr('disabled');
                $("#roadmap1").prop('checked');
                $("#roadmap1").attr('disabled', 'disabled');
            }
            else
            {
                $("#roadmap1").prop('checked', 'checked');
                $("#roadmap1").removeAttr('disabled');
                $("#roadmap0").prop('checked');
                $("#roadmap0").attr('disabled', 'disabled');
            }
            if(visit['hygieneRegulations'] === 0)
            {
                $("#hygieneRegulations0").prop('checked', 'checked');
                $("#hygieneRegulations0").removeAttr('disabled');
                $("#hygieneRegulations1").prop('checked');
                $("#hygieneRegulations1").attr('disabled', 'disabled');
            }
            else
            {
                $("#hygieneRegulations1").prop('checked', 'checked');
                $("#hygieneRegulations1").removeAttr('disabled');
                $("#hygieneRegulations0").prop('checked');
                $("#hygieneRegulations0").attr('disabled', 'disabled');
            }
            $("#reasonForVisit").val(visit['reasonForVisit']);
            $("#userId").val(visit['userId']);
            $("#contactPossibility").val(visit['contactPossibility']);
            $('#visitId').val(visit['visitId']);

            $('#mainDiv').remove();

            if(visit['workPermissionApprovalText'] == "" || visit['workPermissionApprovalText'] == null)
            {
                $('#workPermissionLableDiv').attr('hidden', 'hidden');
            }
            else
            {
                $('#workPermissionLableDiv').removeAttr('hidden');
            }
            addWorkPermissinPDFs(visit['workPermissionDocuments']);

            $.ajax
                ({
                    type: "POST",
                    url: "{{ route('myAdvanceRegistrationGetVisitors') }}",
                    dataType:"json",
                    data:
                        {
                            allocationid:visit['allocationid'],
                            _token:"{{ csrf_token() }}",
                        },
                    success: function(data)
                    {
                        $("#childDiv").empty();
                        $("#visitors").empty();
                        $.each(data['mainVisitors'], function(k, v) {
                            $.each(v, function(kv, vv) {
                                addvisitorElement(vv, visit['allocationid'], "childDiv");
                            });
                        });
                    },
                    error: function (error) {
                        console.log(error);
                    }

                });

                $.ajax
                ({
                    type: "POST",
                    url: "{{ route('myAdvanceRegistrationGetUsers') }}",
                    dataType:"json",
                    data:
                        {
                            allocationid:visit['allocationid'],
                            _token:"{{ csrf_token() }}",
                        },
                    success: function(data)
                    {
                        $("#childUserDiv").empty();
                        $.each(data['users'], function(k, v) {
                            $("#personToVisitLable").removeClass("d-none");
                            $.each(v, function(kv, vv) {
                                addUserElement(vv, visit['allocationid'], "childUserDiv");
                            });
                        });
                    },
                    error: function (error) {
                        console.log(error);
                    }

                });

        }

        function addvisitorElement(data , allocationid, parentId, visitId)
        {
            visitId = visitId || data['visitId'] || null;
            if($("#alertId").length)
            {
                $("#alertId").remove();
            }
            if($("#form-row" + data["id"]).length)
            {
                $("#alerId").remove();
                $("#alertDiv").append($("<div>").addClass("alert alert-danger alert-dismissible").attr("id", "alertId").text("@lang('main.theVisitorHasAlreadyBeenAdded')"));
                $("#alertId").append('<a data-dismiss="alert" aria-label="close" href="#" class="close">&times;</a>');
                window.setTimeout(function() {
                    $(".alert").fadeTo(500, 0).slideUp(500, function(){
                        $(this).remove();
                    });
                }, 4000);
            }
            else
            {
                var divrow = $("<div>").addClass("form-row").attr("id", "form-row" + data["id"]);
                $("#" + parentId).append(divrow);
                var div1 = $('<div>').addClass("form-group col");
                var div2 = $('<div>').addClass("form-group col");
                var div3 = $('<div>').addClass("form-group col");
                $("#form-row" + data["id"]).append(div1, div2, div3);
                var input1 = $('<input>').attr("type", 'text').attr("readonly", "readonly").addClass('form-control transparentBackground').val(data["forename"]);
                var input2 = $('<input>').attr("type", 'text').attr("readonly", "readonly").addClass('form-control transparentBackground').val(data["surname"]);
                var input3 = $('<input>').attr("type", 'text').attr("readonly", "readonly").addClass('form-control transparentBackground').val(data["company"]);
                var input4 = $('<input>').attr("type", 'hidden').addClass('form-control').attr("name", "visitorids[]").val(data["id"]);
                div1.append(input1);
                div2.append(input2);
                div3.append(input3);
                div1.append(input4);
            }
        }


        function addUserElement(data , allocationid, parentId, userId)
        {
            visitId = visitId || data['visitId'] || null;
            if($("#alertId").length)
            {
                $("#alertId").remove();
            }
            if($("#form-row-user" + data["id"]).length)
            {
                $("#alerUserId").remove();
                $("#alertUserDiv").append($("<div>").addClass("alert alert-danger alert-dismissible").attr("id", "alertUserId").text("@lang('main.theUserHasAlreadyBeenAdded')"));
                $("#alertUserId").append('<a data-dismiss="alert" aria-label="close" href="#" class="close">&times;</a>');
                window.setTimeout(function() {
                    $(".alert").fadeTo(500, 0).slideUp(500, function(){
                        $(this).remove();
                    });
                }, 4000);
            }
            else
            {
                var divrow = $("<div>").addClass("form-row").attr("id", "form-row-user" + data["id"]);
                $("#" + parentId).append(divrow);
                var div1 = $('<div>').addClass("form-group col");
                var div2 = $('<div>').addClass("form-group col");
                $("#form-row-user" + data["id"]).append(div1, div2);
                var input1 = $('<input>').attr("type", 'text').attr("readonly", "readonly").addClass('form-control transparentBackground').val(data["forename"]);
                var input2 = $('<input>').attr("type", 'text').attr("readonly", "readonly").addClass('form-control transparentBackground').val(data["surname"]);
                var input3 = $('<input>').attr("type", 'hidden').addClass('form-control').attr("name", "userids[]").val(data["id"]);
                div1.append(input1);
                div2.append(input2);
                div2.append(input3);
            }
        }

        function addWorkPermissinPDFs(data)
        {
            var mainDiv = $("<div>").attr("id", "mainDiv");
            $('#workPermissionDocumentList').append(mainDiv);

            $.each(data, function(k, v) {
                var div = $("<div>").attr('class', "form-row").attr("id", "div" + v['name']);
                var div1 = $("<div>").attr('class', "form-group col");
                mainDiv.append(div);
                var input = $("<input>").attr("type", "hidden").attr("value", v['url']).attr("name", "wpd[]");
                div.append(div1).append(input);
                var a = $("<a>").attr("href", v['url']).attr("target","_blank").attr("rel","noopener").text(v['name']);
                var li = $("<li>").append(a);
                var ul = $("<ul>").append(li);
                div1.append(ul);

            });
        }

        function removeVisitor(removeId)
        {
            $("#form-row" + removeId).remove();
        }
        function removeUser(removeId)
        {
            $("#form-row-user" + removeId).remove();
        }


        function getCSV()
        {
            var activeVisitsOnly = $("#activeVisits").attr('checked') == "checked" ? true : false;
            $.ajax
                ({
                    type: "GET",
                    url: "{{ route('visits.history.export.csv') }}?activeVisits=" + activeVisitsOnly + "&visitorDetail=" + $("#visitorDetail").val() + "&von=" + $("#von").val() + "&bis=" + $("#bis").val() + "&search=" + $("#search").val() + "&items=1000000",
                    success: function(data)
                    {
                        var win = window.open(data, '_blank');
                        win.focus();
                    },
                    error: function (error) {
                        console.log(error);
                    }

                });
        }

        function editAdvancedRegistration(id, type) {
            $('#FormTest')[0].reset();
            $('#makeVisitForm')[0].reset();
            $.ajax({
                type: "GET",
                url: "/api/newAdvancedRegistration/" + id,
                dataType:"json",
                success: function(data){
                    document.getElementById('editARButton').onclick = function ()
                    {
                        editAdvanceRegistrationFormSender(data["id"])
                    };
                    $('#mainDiv').remove();
                    if(data['workPermissionDocuments'] != null)
                    {
                        var mainDiv = $("<div>").attr("id", "mainDiv");
                        $('#workPermissionDocumentList').append(mainDiv);
                        $.each(data['workPermissionDocuments'], function(k, v) {
                            var div = $("<div>").attr('class', "form-row").attr("id", "div" + v['name']);
                            var div1 = $("<div>").attr('class', "form-group col");
                            var div2 = $("<div>").attr('class', "form-group col");
                            mainDiv.append(div);
                            div.append(div1).append(div2);
                            var a = $("<a>").attr("href", v['url']).attr("target","_blank").attr("rel","noopener").text(v['name']);
                            var li = $("<li>").append(a);
                            var ul = $("<ul>").append(li);
                            div1.append(ul);
                            var buttonDelete = $('<button>').addClass("btn btn-outline-danger fa-trash-alt fa").attr("id",v['name']).attr("type", "button");
                            div2.append(buttonDelete);
                            document.getElementById('cancelDeleteWorkPermissionDocument').onclick = function(e)
                            {
                                $("#deleteWorkPermissionDocumentModal").modal('hide');
                            };

                            document.getElementById(v['name']).onclick = function()
                            {
                                deletemomDocuments(v['name'],v['url'])
                            };
                        });
                    }

                    $('#startDate').val(data['startDate']);
                    $("#startTime").val(data['startTime']);
                    $("#endDate").val(data['endDate']);
                    $("#endTime").val(data['endTime']);
                    if(data['hygieneRegulations'] === 0)
                    {
                        $("#hygieneRegulations0").attr('checked', 'checked');
                        $("#hygieneRegulations1").removeAttr('checked');
                    }
                    else
                    {
                        $("#hygieneRegulations1").attr('checked', 'checked');
                        $("#hygieneRegulations0").removeAttr('checked');
                    }
                    if(data['roadmap'] === 0)
                    {
                        $("#roadmap").attr('checked', 'checked');
                        $("#roadmap").removeAttr('checked');
                    }
                    else
                    {
                        $("#roadmap").attr('checked', 'checked');
                        $("#roadmap").removeAttr('checked');
                    }
                    $("#reasonForVisit").val(data['reasonForVisit']);
                    $("#userId").val(data['userId']);
                    $("#contactPossibility").val(data['contactPossibility']);
                    $('#visitId').val(data['visitId']);
                    var allocationid = data['allocationid'];
                    $.ajax
                    ({
                        type: "POST",
                        url: "{{ route('myAdvanceRegistrationGetVisitors') }}",
                        dataType:"json",
                        data:
                            {
                                allocationid:data['allocationid'],
                                _token:"{{ csrf_token() }}",
                            },
                        success: function(data)
                        {
                            $("#childDiv").empty();
                            $("#visitors").empty();
                            $.each(data['mainVisitors'], function(k, v) {
                                $.each(v, function(kv, vv) {
                                    addvisitorElement(vv, allocationid, "childDiv");
                                });
                            });
                        },
                        error: function (error) {
                            console.log(error);
                        }

                    });

                    $.ajax
                    ({
                        type: "POST",
                        url: "{{ route('myAdvanceRegistrationGetUsers') }}",
                        dataType:"json",
                        data:
                            {
                                allocationid:data['allocationid'],
                                _token:"{{ csrf_token() }}",
                            },
                        success: function(data)
                        {
                            $("#childUserDiv").empty();
                            $.each(data['users'], function(k, v) {
                                $.each(v, function(kv, vv) {
                                    addUserElement(vv, allocationid, "childUserDiv");
                                });
                            });
                        },
                        error: function (error) {
                            console.log(error);
                        }

                    });
                }
            });
        }
    </script>

@endsection
