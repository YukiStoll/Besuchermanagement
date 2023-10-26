@extends('layouts.layout')
@section('content')
    <div id="successdiv"></div>
    @if((isset($requestData) && $requestData["hasSearchCondition"] != "false") || !$data->isEmpty())
    <div class="container w-100 mw-100 table-responsive">
        <h3>@lang('main.advanceRegistrationOverview')</h3>
        <form method="get" id="searchForm" action="@canany(['isgatekeeper','isadmin', 'issuperadmin']) {{ route('gatekeeperAdvanceRegistration') }} @endcan @can('isemployee') {{ route('myAdvanceRegistration') }} @endcan">
            <div class="form-row">
                    <div class="justify-content-start col-4">
                        <div class="form-row">
                            @can('isgatekeeper')
                                <div class="form-group col-auto">
                                    <button type="submit" class="btn form-control btn-primary fa fa-search"></button>
                                </div>
                                <div class="form-group col-5">
                                    <input id="visitIDsearch" name="visitIDsearch" @isset($requestData['visitIDsearch']) value="{{ $requestData['visitIDsearch'] }}" @endisset class="form-control" type="text" placeholder="@lang('main.visitIDsearch')">
                                </div>
                            @endcan
                            @canany(['isadmin', 'issuperadmin'])
                                <input id="visitIDsearch" name="visitIDsearch" @isset($requestData['visitIDsearch']) value="{{ $requestData['visitIDsearch'] }}" @endisset class="form-control" type="hidden">
                            @endcan
                            @cannot('isgatekeeper')
                                <div class="form-group col-auto">
                                    <input type="checkbox" id="myAdvanceRegistration" name="myAdvanceRegistration" @if(isset($myAdvanceRegistration)) checked @endif>
                                    <label for="myAdvanceRegistration">@lang('main.showOnlyMyAdvanceRegistration')</label>
                                </div>
                            @endcan
                        </div>
                    </div>

                <div class="justify-content-end col">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="col-form-label-lg">Von</label>
                        </div>

                        <div class="form-group col-auto">
                            <input name="von" @isset($requestData['startDate']) value="{{ $requestData['startDate'] }}" @endisset class="form-control form-inline" type="date">
                        </div>

                        <div class="form-group">
                            <label class="col-form-label-lg">Bis</label>
                        </div>

                        <div class="form-group col-auto">
                            <input name="bis" @isset($requestData['endDate']) value="{{ $requestData['endDate'] }}" @endisset class="form-control" type="date">
                        </div>

                        <div class="form-group col-5">
                            <input name="search" @isset($requestData['search']) value="{{ $requestData['search'] }}" @endisset class="form-control" type="text" placeholder="@lang('main.searchAfterSnameFnameCompany')">
                        </div>
                        <input name="items" value="{{ $pagitems }}" type="hidden">
                        <div class="form-group col-auto">
                            <button type="submit" class="btn form-control btn-primary fa fa-search"></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div><hr class="invisible"></div>
        <div>
            <div class="row">
                <div class="col">
                    <table class="table table-hover table-striped table-small-padding">
                        <thead @if(env("APP_table_Color")) class="table-dark" style="background: {{ env("APP_table_Color") }}" @else class="thead-dark" @endif>
                        <tr>
                            <th scope="col">@sortablelink('startDate', __('main.startDate'))</th>
                            <th scope="col">@sortablelink('endDate', __('main.endDate'))</th>
                            <th scope="col">@sortablelink('Company', __('main.company'))</th>
                            <th scope="col">@sortablelink('Visitor', __('main.visitor'))</th>
                            <th scope="col">@sortablelink('visitorCategory', __('main.visitorCategory'))</th>
                            <th scope="col">@sortablelink('visitorDetail', __('main.visitorDetail'))</th>
                            <th scope="col">@sortablelink('name', __('main.employee'))</th>
                            <th scope="col">@sortablelink('visitId', __('main.visitId'))</th>
                            <th scope="col">@sortablelink('created_at', __('main.createdAt'))</th>
                            <th scope="col">@sortablelink('updated_at', __('main.updatedAt'))</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($requestData) && empty($requestData))
                        @else
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
                                    <td class="text-right" nowrap="nowrap">
                                        @if ($items->entrypermission == 'granted')
                                            <i class="btn btn-outline-success fa-car fa"></i>
                                        @elseif ($items->entrypermission == 'pending')
                                            <i class="btn btn-outline-warning fa-car fa"></i>
                                        @elseif ($items->entrypermission == 'denied')
                                            <i class="btn btn-outline-danger fa-car fa"></i>
                                        @endif
                                        @if (Auth::user()->id == $items->userId || Auth::user()->role == "Admin" || Auth::user()->role == "Gatekeeper" || Auth::user()->role == "Super Admin")
                                            @cannot('isgatekeeper') <button type="button" data-target="#Delete" data-toggle="modal" onclick="removeAdvancedRegistration({{ $items->id }})" class="btn btn-outline-danger fa-trash-alt fa"></button> @endcan
                                            <button type="button" data-target="#Edit" data-toggle="modal" onclick="editAdvancedRegistration({{ $items->id }})" class="btn btn-outline-success icon-pencil align-right"></button>
                                            @canany(['isgatekeeper','isadmin', 'issuperadmin']) <button type="button" onclick="getVisit({{ $items->visitId }})" class="btn btn-outline-primary fa fa-door-open align-right"></button> @endcan
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    <div>
                        <div class="btn-group float-right">
                            <a class="btn @if($pagitems == 5) btn-dark @else btn-light @endif btn-sm" @if($pagitems == 5) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '5','von' => $requestData['startDate'],'bis' => $requestData['endDate'],'search' => $requestData['search'], 'myAdvanceRegistration' => $requestData['myAdvanceRegistration'], 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">5</a>
                            <a class="btn @if($pagitems == 10) btn-dark @else btn-light @endif btn-sm" @if($pagitems == 10) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '10','von' => $requestData['startDate'],'bis' => $requestData['endDate'],'search' => $requestData['search'], 'myAdvanceRegistration' => $requestData['myAdvanceRegistration'], 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">10</a>
                            <a class="btn @if($pagitems == 25) btn-dark @else btn-light @endif btn-sm" @if($pagitems == 25) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '25','von' => $requestData['startDate'],'bis' => $requestData['endDate'],'search' => $requestData['search'], 'myAdvanceRegistration' => $requestData['myAdvanceRegistration'], 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">25</a>
                            <a class="btn @if($pagitems == 50) btn-dark @else btn-light @endif btn-sm" @if($pagitems == 50) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '50','von' => $requestData['startDate'],'bis' => $requestData['endDate'],'search' => $requestData['search'], 'myAdvanceRegistration' => $requestData['myAdvanceRegistration'], 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">50</a>
                        </div>
                        {{ $data->appends(
                            [
                                'items' => $pagitems,
                                'von' => $requestData['startDate'],
                                'bis' => $requestData['endDate'],
                                'search' => $requestData['search'],
                                'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "",
                                'direction' => isset($_GET["direction"]) ? $_GET["direction"] : "",
                            ]
                        )->links() }}
                    </div>

                </div>
            </div>
        </div>
        @else
            <h2>@lang('main.noAdvancedRegistrations')</h2>
        @endif
        <div class="modal fade" id="Delete" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h2 class="text-center w-100">@lang('main.Q_DeleteTheAdvancedRegistration')</h2>
                    </div>
                    <div class="modal-footer">
                        <div class="form-group col">
                            <button id="deleteAdvancedRegistration" type="button" class="btn btn-outline-danger btn-full">@lang('main.delete')</button>
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
                            <h4 class="modal-title" id="exampleModalLabel">@lang('main.editAdvancedRegistration')</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @include('editAdvanceRegistrationForm')
                        </div>
                        <div class="modal-footer">
                            <button id="editARButton" type="button" class="btn btn-primary col-12">@lang('main.save')</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="makeVisit" tabindex="-1" role="dialog" aria-labelledby="EditlLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h6 class="modal-title" id="exampleModalLabel">@lang('main.makeVisit')</h6>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						@include('makeVisitForm', [ 'requestData' => $requestData ])
					</div>
					<div class="modal-footer">
						<button id="askMakeVisitButton" type="button" onclick="askMakeVisit()" class="btn btn-primary col-12 saveButton">@lang('main.createVisitFromAdvanceRegistration')</button>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="askMakeVisit" tabindex="-1" role="dialog" aria-labelledby="EditlLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h6 class="modal-title" id="exampleModalLabel">@lang('main.makeVisit')</h6>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						@lang('main.stillConvertAdvanceRegistrationToVisit')
					</div>
					<div class="modal-footer">
                        <div class="form-group col">
						<button id="makeVisitButton" type="button" class="btn btn-primary col-12 saveButton">@lang('main.yes')</button>
                        </div>
                        <div class="form-group col">
						<button id="" type="button" data-toggle="modal" data-target="#askMakeVisit" class="btn btn-primary col-12 ">@lang('main.cancel')</button>
                        </div>
					</div>
				</div>
			</div>
		</div>

@endsection
@section('scripts')
<script>
    $( "#myAdvanceRegistration" ).click(function() {
        $('#visitIDsearch').val("");
        $( "#searchForm" ).submit();
    });

    jQuery(document).ready(function($) {
        var engine = new Bloodhound({
            remote: {
                url: "{{ route('newVisitor.search') }}?query=%QUERY%",
                wildcard: '%QUERY%'
            },
            datumTokenizer: Bloodhound.tokenizers.whitespace("query"),
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });
        $(".search-input").typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            source: engine.ttAdapter(),
            name: 'usersList',
            limit: 100,
            templates: {
                empty: [
                    '<div class="list-group search-results-dropdown"><div class="list-group-item">Nothing found.</div></div>'
                ],
                header: [
                    '<div class="list-group search-results-dropdown">'
                ],
                suggestion: function (data) {
                    return '<li class="list-group-item">' + data["forename"] + " " + data["surname"] + " " + data["company"] + '</li>'
                }
            }
        }).on('typeahead:selected typeahead:autocompleted', function(event, data) {
            $(this).typeahead('val', '');
            addvisitorElement(data, null, "childDiv", $('#visitId').val());
        })
    });


    jQuery(document).ready(function($) {
        setTypeaheadForUsers();
        });

    function deletemomDocuments(name, url)
    {
        $("#deleteWorkPermissionDocumentModal").modal('show');
        document.getElementById("deleteWorkPermissionDocument").onclick = function(e)
        {
            var file = url.replace("{{ URL::to('/') }}/", "");
            file = file.replace("/", "\\");
            file = file.replace("/", "\\");
            if(e.button === 0) {
                $.ajax
                ({
                    type: "POST",
                    url: "{{ route("AdvancedRegistration.fileDelete") }}",
                    dataType:"json",
                    data:
                        {
                            url:file,
                            _token:"{{ csrf_token() }}",
                        },
                    success: function()
                    {
                        document.getElementById("div" + name).remove();
                        $("#deleteWorkPermissionDocumentModal").modal('hide');
                    },
                    error: function (error) {
                        console.log(error);
                    }

                });
            }
        };
    }

    $('#pdf').change(function () {
        var form = document.getElementById("FormTest");
        $.ajax({
            type: "POST",
            url: "{{ route('AdvancedRegistration.tempSaveDocuments') }}",
            dataType:"json",
            processData:false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:new FormData(form),
            success: function(data){
                var mainDiv = $("<div>").attr("id", "mainDiv");
                $('#workPermissionDocumentList').append(mainDiv);

                $.each(data['files'], function(k, v) {
                    var div = $("<div>").attr('class', "form-row").attr("id", "div" + v['name']);
                    var div1 = $("<div>").attr('class', "form-group col");
                    var div2 = $("<div>").attr('class', "form-group col");
                    mainDiv.append(div);
                    var input = $("<input>").attr("type", "hidden").attr("value", v['url']).attr("name", "wpd[]");
                    div.append(div1).append(div2).append(input);
                    var a = $("<a>").attr("href", v['url']).attr("target","_blank").attr("rel","noopener").text(v['name']);
                    var li = $("<li>").append(a);
                    var ul = $("<ul>").append(li);
                    div1.append(ul);
                    var buttonDelete = $('<button>').addClass("btn btn-outline-danger fa-trash-alt fa").attr("id",v['name']).attr("type", "button");
                    div2.append(buttonDelete);
                    document.getElementById(v['name']).onclick = function()
                    {
                        deletemomDocuments(v['name'],v['url'])
                    };

                });

            }
        });
    });
    function editAdvancedRegistration(editId) {
        $('#FormTest')[0].reset();
        $('#makeVisitForm')[0].reset();
        $('#allocationid').remove();
        $.ajax({
            type: "GET",
            url: "/api/newAdvancedRegistration/" + editId,
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

                $("#editEntrypermissionID > option").removeAttr('selected', 'selected');
                $("#editWorkPermissionID > option").removeAttr('selected', 'selected');
                $("#editEntrypermissionID > option[value=" + data['entrypermissionID'] + "]").attr('selected', 'selected');
                $("#editWorkPermissionID > option[value=" + data['workPermissionID'] + "]").attr('selected', 'selected');
                $('#editWorkPermissionApprovalText').val(data['workPermissionApprovalText']);
                $('#editEntryPermissionText').val(data['entryPermissionText']);

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
                                addvisitorElement(v, allocationid, "childDiv");
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

    function editAdvanceRegistrationFormSender(visitorId)
    {
        var visitorids = $('input[name="visitorids[]"]').map(function(){
            return this.value;
        }).get();

        var userids = $('input[name="userids[]"]').map(function(){
            return this.value;
        }).get();


        var tableids = $('input[name="tableids[]"]').map(function(){
            return this.value;
        }).get();

        var groupMemberForename = $('input[name="groupMemberForename[]"]').map(function(){
            return this.value;
        }).get();

        var groupMemberSurname = $('input[name="groupMemberSurname[]"]').map(function(){
            return this.value;
        }).get();

        var newGroupMember = $('input[name="new[]"]').map(function(){
            return this.value;
        }).get();

        var groupMemberCanteen = $('input[name="groupMemberCanteen[]"]').map(function(){
            return this.value;
        }).get();

        var canteenIds = $('input[name="canteenIds[]"]').map(function(){
            return this.value;
        }).get();

        $.ajax({
            type: "PUT",
            url: "/api/newAdvancedRegistration/" + visitorId,
            dataType:"json",
            data: {
                startDate:$("#startDate").val(),
                startTime:$("#startTime").val(),
                endDate:$("#endDate").val(),
                endTime:$("#endTime").val(),
                roadmap:$("#roadmap").val(),
                entrypermissionID:$("#editEntrypermissionID").val(),
                workPermissionID:$("#editWorkPermissionID").val(),
                entryPermissionText:$("#editEntryPermissionText").val(),
                workPermissionApprovalText:$("#editWorkPermissionApprovalText").val(),
                allocationid:$("input[name=allocationid]").val(),
                canteen:1,
                reasonForVisit:$('#reasonForVisit').val(),
                userId:$('#userId').val(),
                groupMemberForename:groupMemberForename,
                groupMemberSurname:groupMemberSurname,
                groupMemberCanteen:groupMemberCanteen,
                new:newGroupMember,
                visitorids:visitorids,
                userids:userids,
                tableids:tableids,
                canteenIds:canteenIds,
                contactPossibility:$("#contactPossibility").val(),
                _token:"{{ csrf_token() }}",
            },
            success: function(data)
            {
                //Anfrage machen um distinct zu bekommen welche ids bereits verwendet werden bevor sie geupdated werden um sie nach dem Update vergleichen zu können.
                var existingMawaIDs = "";
                $.ajax({
                        type: "get",
                        url: "/api/getMawaIDs/" + $("input[name=allocationid]").val(),
                        success: function(data)
                        {
                            existingMawaIDs = data;
                            var newMawaIDs = [];
                            var test = 0;
                            var promise = new Promise((resolve, reject) => {
                                        visitorids.forEach((items, index, array) => {
                                            test++;
                                            $.ajax({
                                            type: "POST",
                                            url: "/api/updataMawaIDForVisitor/" + items,
                                            dataType:"json",
                                            data:{
                                                allocationid:$("input[name=allocationid]").val(),
                                                mawaIDs:[...document.getElementById("areaPermissionSelect-" + items + "[]").selectedOptions].map(option => option.value),
                                                _token:"{{ csrf_token() }}",
                                            },
                                            success: function(data)
                                            {
                                                console.log("test");
                                                if(data.success && data.change != null)
                                                {
                                                    if(typeof data.change === 'object')
                                                    {
                                                        for (const [key, value] of Object.entries(data.change))
                                                        {
                                                            newMawaIDs.indexOf(value) === -1 ? newMawaIDs.push(value) : null;

                                                        }
                                                    }
                                                    else
                                                    {
                                                        newMawaIDs.indexOf(data.change) === -1 ? newMawaIDs.push(data.change) : null;
                                                    }
                                                }
                                                test--;
                                                if(test == 0) resolve();
                                            }
                                        })

                                    });
                                    });

                            promise.then(() => {
                                //Muss überall hinzugefügt werden
                                //Benötigt alle Besucher die genehmigt werden müssen hierzu
                                //anhand der allocation id und den mawa ids die Besucher filtern für die die genehmigung gesendet werden muss.
                                if(newMawaIDs.length != 0)
                                {
                                    $.ajax({
                                            type: "POST",
                                            url: "/api/sendMawaPermissionEMail/" + $("input[name=allocationid]").val(),
                                            dataType:"json",
                                            data:{
                                                existingMawaIDs:existingMawaIDs,
                                                newMawaIDs:newMawaIDs,
                                                _token:"{{ csrf_token() }}",
                                            },
                                            success: function(data)
                                            {
                                                console.log(data);
                                            }
                                        })
                                }
                            });
                        }
                    })

                $.ajax({
                    type: "POST",
                    url: "/api/newAdvancedRegistration/fileUpload/" + data['visitId'],
                    dataType:"json",
                    processData:false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:new FormData(document.getElementById('FormTest')),
                    success: function()
                    {
                        $('#Edit').modal('toggle');
                        $("#alerId").remove();
                        var trest = ($('<div>').addClass("alert alert-success alert-dismissible").attr("id", "alertId").text("@lang('main.successEditVisitorMessage')"));
                        $("#successdiv").append(trest);
                        trest.append('<a aria-label="close" data-dismiss="alert" href="#" class="close">&times;</a>');
                        window.setTimeout(function() {
                            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                                $(this).remove();
                            });
                        }, 10000);
                    }
                });
            }
        });

    }

    function removeAdvancedRegistration(removeId) {

        $("#deleteAdvancedRegistration").attr("data-dismiss", "modal");
        document.getElementById("deleteAdvancedRegistration").onclick = function() {
            $.ajax({
                type: "DELETE",
                url: "/api/newAdvancedRegistration/" + removeId,
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
                            .text("@lang('main.successDeletedAdvancedRegistrationMessage')")
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
            var div4 = $('<div>').addClass("form-group col").attr("id", "mawarow-" + data["id"]);
            var div5 = $('<div>').addClass("form-group col");
            $("#form-row" + data["id"]).append(div1, div2, div3, div4, div5);
            var input1 = $('<input>').attr("type", 'text').attr("readonly", "readonly").addClass('form-control').val(data["forename"]);
            var input2 = $('<input>').attr("type", 'text').attr("readonly", "readonly").addClass('form-control').val(data["surname"]);
            var input3 = $('<input>').attr("type", 'text').attr("readonly", "readonly").addClass('form-control').val(data["company"]);
            var input4 = $('<input>').attr("type", 'hidden').addClass('form-control').attr("name", "visitorids[]").val(data["id"]);
            //var input5 = $('<input>').attr("type", 'hidden').addClass('form-control').attr("name", "tableids[]").val(1);
            var buttonDelete = $('<button>').addClass("btn btn-outline-danger fa-trash-alt fa").attr("id","delete" + data["id"]).attr("type", "button");
            var buttonCanteen = $('<button>').addClass("btn btn-outline-secondary fa fa-coffee align-right").attr("id","canteen" + data["id"]).attr("type", "button");
            var QRCodeButton = $('<button>').addClass("btn btn-outline-secondary fa fa-qrcode").attr("id",data["id"] + "-1").attr('type','button');
            var spacer = $('<span>').text = " ";

            var select = $('<select>').attr("id", "areaPermissionSelect-" + data["id"] + "[]").addClass('form-control selectpicker').attr("name", "areaPermissionSelect-" + data["id"] + "[]").attr("data-live-search", "true").attr("multiple", "multiple").attr("data-actions-box", "true").attr("title", "@lang('main.areaPermission')");
            var areaPermissions = {!! json_encode($areaPermissions) !!};
            var selectedAreaPermissions = data["mawaAreaIds"] != null ? data["mawaAreaIds"] : ["-1"];
            var selectedAreaPermissionsIds = [];
            selectedAreaPermissions.forEach(item => selectedAreaPermissionsIds.push(item['areapermissionID']));

            areaPermissions.forEach(item => select.append($('<option>').val(item.id).text(item.name).attr("id", "option-" + data["id"] + "-" + item.id).attr("selected", selectedAreaPermissionsIds.includes(item.id))));


            div1.append(input1);
            div1.append(input4);
            div2.append(input2);
            div3.append(input3);
            //div1.append(input5);
            div4.append(select);
            div5.append(buttonDelete);
            div5.append(spacer);
            div5.append(buttonCanteen);
            div5.append(spacer);
            div5.append(QRCodeButton);

            select.selectpicker();

                                            const targetNode = document.getElementById("mawarow-" + data["id"]);
                                            const targetNodeChild = findFirstChildByClass(targetNode, "inner");
                                            const config = { childList: true };
                                            const callback = (mutationList, observer) => {
                                            for (const mutation of mutationList) {
                                                if (mutation.type === "childList") {
                                                    var selectedElements = mutation.target.querySelectorAll( "li.selected" );
                                                    for(i = 0; i < selectedElements.length; i++)
                                                    {
                                                        var spanSelectedElements = selectedElements.item(i).querySelectorAll( ".text" );
                                                        for(p = 0; p < spanSelectedElements.length; p++)
                                                        {
                                                            selectedAreaPermissions.forEach(item =>
                                                            {
                                                                if(spanSelectedElements.item(p).textContent == item['name'])
                                                                {
                                                                    if(item['status'] == "granted")
                                                                    {
                                                                        spanSelectedElements.item(p).classList.add("text-success");
                                                                    }
                                                                    else if(item['status'] == "denied")
                                                                    {
                                                                        spanSelectedElements.item(p).classList.add("text-danger");
                                                                    }
                                                                    else
                                                                    {
                                                                        spanSelectedElements.item(p).classList.add("text-warning");
                                                                    }
                                                                }
                                                            });
                                                        }
                                                    }
                                                }
                                            }
                                            };

                                            const observer = new MutationObserver(callback);
                                            observer.observe(targetNodeChild, config);

                                            selectedAreaPermissions.forEach(item =>
                                            {
                                                for (const a of targetNode.querySelectorAll(".filter-option-inner-inner"))
                                                {
                                                    if (a.textContent.includes(item['name']))
                                                    {
                                                        var replace = escapeHtml(item['name']);
                                                        if(item['status'] == "granted")
                                                        {
                                                            a.innerHTML = a.innerHTML.replace(replace, '<span class="text-success">' + item['name'] + '</span>');
                                                        }
                                                        else if(item['status'] == "denied")
                                                        {
                                                            a.innerHTML = a.innerHTML.replace(replace, '<span class="text-danger">' + item['name'] + '</span>');
                                                        }
                                                        else
                                                        {
                                                            a.innerHTML = a.innerHTML.replace(replace, '<span class="text-warning">' + item['name'] + '</span>');
                                                        }
                                                    }
                                                }
                                            });



            QRCodeButton.click(function(e)
            {
                var win = window.open("/printQRCode/" + visitId + '-' + data["id"] + '-1' + "/" + data['forename'] + " " + data['surname'], '_blank');
                win.focus();
            });
            if(allocationid && !$('#allocationid').length)
            {
                var input7 = $('<input>').attr("type", 'hidden').addClass('form-control').attr("name", "allocationid").attr("id", "allocationid").val(allocationid);
                $("#allocation").append(input7);
            }
            if(data['canteen'] === 1)
            {
                buttonCanteen.removeClass("btn-outline-secondary");
                buttonCanteen.addClass("btn-success");
                var input6 = $('<input>').attr("type", 'hidden').addClass('form-control').attr("id", "canteen" + data["id"]).attr("name", "canteenIds[]").val(data["id"]);
                div1.append(input6);
            }
            document.getElementById("delete" + data["id"]).onclick = function(e)
            {
                if(e.button === 0) {
                    removeVisitor(data["id"]);
                }
            };
            buttonCanteen.click(function () {
                if($(this).hasClass("btn-success"))
                {
                    $("#canteen" + data["id"]).remove();
                    $(this).removeClass("btn-success");
                    $(this).addClass("btn-outline-secondary");
                }
                else
                {
                    var input6 = $('<input>').attr("type", 'hidden').addClass('form-control').attr("id", "canteen" + data["id"]).attr("name", "canteenIds[]").val(data["id"]);
                    div1.append(input6);
                    $(this).addClass("btn-success");
                    $(this).removeClass("btn-outline-secondary");
                }
            });
        }
    }









    function findFirstChildByClass(element, className) {
        var foundElement = null, found;
        function recurse(element, className, found) {
            for (var i = 0; i < element.childNodes.length && !found; i++) {
                var el = element.childNodes[i];
                var classes = el.className != undefined? el.className.split(" ") : [];
                for (var j = 0, jl = classes.length; j < jl; j++) {
                    if (classes[j] == className) {
                        found = true;
                        foundElement = element.childNodes[i];
                        break;
                    }
                }
                if(found)
                    break;
                recurse(element.childNodes[i], className, found);
            }
        }
        recurse(element, className, false);
        return foundElement;
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
            var div3 = $('<div>').addClass("form-group col-1");
            $("#form-row-user" + data["id"]).append(div1, div2, div3);
            var input1 = $('<input>').attr("type", 'text').attr("readonly", "readonly").addClass('form-control').val(data["forename"]);
            var input2 = $('<input>').attr("type", 'text').attr("readonly", "readonly").addClass('form-control').val(data["surname"]);
            var input3 = $('<input>').attr("type", 'hidden').addClass('form-control').attr("name", "userids[]").val(data["id"]);
            var buttonDelete = $('<button>').addClass("btn btn-outline-danger fa-trash-alt fa").attr("id","delete-user" + data["id"]).attr("type", "button");
            var spacer = $('<span>').text = " ";
            div1.append(input1);
            div2.append(input2);
            div2.append(input3);
            div3.append(buttonDelete);
            if(allocationid)
            {
                var input7 = $('<input>').attr("type", 'hidden').addClass('form-control').attr("name", "allocationUserid").attr("id", "allocationUserid").val(allocationid);
                $("#allocation").append(input7);
            }
            document.getElementById("delete-user" + data["id"]).onclick = function(e)
            {
                if(e.button === 0) {
                    removeUser(data["id"]);
                }
            };
        }
    }

    function removeVisitor(removeId)
    {
        $("#form-row" + removeId).remove();
    }
    function removeUser(removeId)
    {
        $("#form-row-user" + removeId).remove();
    }

    function getVisit(id) {
        $('#visitIDsearch').val(id);
        $('#searchForm').submit();
    }

	function askMakeVisit()
	{
		$('#makeVisit').modal('toggle')
		$('#askMakeVisit').modal('toggle')
	}


    let startDate = $("#startDate");
    let endDate = $("#endDate");
    let startTime = $("#startTime");
    let endTime = $("#endTime");
    startDate.blur(function () {
        if(startDate.val() > endDate.val())
        {
            endDate.val(startDate.val());
        }
        setTypeaheadForUsers();
    });
    endDate.blur(function () {
        if(endDate.val() < startDate.val())
        {
            startDate.val(endDate.val());
        }
        setTypeaheadForUsers();
    });
    startTime.blur(function () {
        if(startDate.val() === endDate.val() && startTime.val() > endTime.val())
        {
            endTime.val(startTime.val());
        }
        setTypeaheadForUsers();
    });
    endTime.blur(function () {
        if(startDate.val() === endDate.val() && startTime.val() > endTime.val())
        {
            startTime.val(endTime.val());
        }
        setTypeaheadForUsers();
    });
    var userURLTypeahead = "{{ route('user.search') }}?query=%QUERY%&startDate=" + $('#startDate').val() + "&endDate=" + $('#endDate').val();
    var engine = new Bloodhound({
                remote: {
                    url: userURLTypeahead,
                    wildcard: '%QUERY%'
                },
                datumTokenizer: Bloodhound.tokenizers.whitespace("query"),
                queryTokenizer: Bloodhound.tokenizers.whitespace
            });

    function setTypeaheadForUsers()
    {
        $('.search-user-input').typeahead('destroy');
        $('.search-user-input').unbind();
        userURLTypeahead = "{{ route('user.search') }}?query=%QUERY%&startDate=" + $('#startDate').val() + "&endDate=" + $('#endDate').val();
        engine = new Bloodhound({
                remote: {
                    url: userURLTypeahead,
                    wildcard: '%QUERY%'
                },
                datumTokenizer: Bloodhound.tokenizers.whitespace("query"),
                queryTokenizer: Bloodhound.tokenizers.whitespace
            });
        $(".search-user-input").typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            source: engine.ttAdapter(),
            display:'term',
            templates: {
                empty: [
                    '<div class="list-group search-results-dropdown"><div class="list-group-item">Nothing found.</div></div>'
                ],
                header: [
                    '<div class="list-group search-results-dropdown">'
                ],
                suggestion: function (data) {
                    return '<li class="list-group-item">' + data["forename"] + " " + data["surname"] + '</li>'
                }
            },
            limit: 100,
        }).on('typeahead:selected typeahead:autocompleted', function(event, data) {
            $(this).typeahead('val', '');
            addUserElement(data, null, "childUserDiv", $('#visitId').val());
        })
    }

</script>
@endsection
