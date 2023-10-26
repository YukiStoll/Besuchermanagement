@extends('layouts.layout')
@section('content')



<div id="alertDivUser">
</div>

@if(isset($success) && !empty($success))
    @if ($success == "savedSuccess")
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            @lang('main.successSaveMawaMessage')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
@endif


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h3>@lang('main.mawaEdit')</h3>
                @if(isset($areapermissions) && empty($areapermissions))
                @else
                <form method="POST" action="{{ route('mawa.edit.user', $areapermissions->id) }}">
                    <div class="form-row">
                        <div class="form-group col">
                            <label>@lang('main.name')</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{  $areapermissions->name }}" autofocus>
                            @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group col">
                            <label>@lang('main.mawaID')</label>
                            <input id="mawaID" type="text" class="form-control @error('mawaID') is-invalid @enderror" name="mawaID" value="{{  $areapermissions->mawaID }}" autofocus>
                            @error('mawaID')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    <input type="hidden" id="id" name="id" value="{{ $areapermissions->id }}">

                    @csrf



                    <table class="table table-hover table-striped">
                        <thead @if(env("APP_table_Color")) class="table-dark" style="background: {{ env("APP_table_Color") }}" @else class="thead-dark" @endif>
                        <tr>
                            <th></th>
                            <th class="col-10">@lang('main.name')</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody id="permitTable">
                        @if(isset($users) && empty($users))
                        @else
                            @foreach($users as $user)
                                <tr id="{{ $user->id }}" userID="{{ $user->userID }}" position="{{ $user->position }}">
                                    <td class="text-right" nowrap="nowrap">
                                        <button type="button" name="upButton" onclick="up(this, 'permitTable')" class="btn btn-sm btn-outline-primary icon-arrow-up align-right"></button>
                                        <button type="button" name="downButton" onclick="down(this, 'permitTable')" class="btn btn-sm btn-outline-primary icon-arrow-down align-right"></button>
                                    </td>
                                    <td>{{ $user->forename }} {{ $user->surname }}</td>
                                    <td class="text-right" nowrap="nowrap">
                                        <button type="button" onclick="removeUser({{ $user->id }})" class="btn btn-outline-danger fa-trash-alt fa"></button>
                                    </td>
                                </tr>
                            @endforeach

                            <tr id="addUserBevorRow">
                                <td></td>
                                <td>
                                    <div class="form-row">
                                        <div class="form-group col">
                                            <input id="searchUser" autocomplete="off" class="search-user-input form-control @error('userIds') is-invalid @enderror" placeholder="@lang('main.searchAfterUser')">
                                        </div>
                                    </div>
                                </td>
                                <td></td>
                            </tr>



                        @endif
                        </tbody>
                    </table>




                    <div class="form-row">
                        <div class="form-group col">
                            <button class="btn btn-outline-primary w-100 saveButton">@lang("main.save")</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
                @endif
            </table>
        </div>
    </div>
</div>


@endsection
@section('scripts')
<script>

    function up(button, tableName)
    {
        var currentRow = button.parentNode.parentNode;
        var table = document.getElementById(tableName);
        var rows = table.getElementsByTagName('tr');
        var newRow = rows[currentRow.rowIndex - 2];
        table.insertBefore(currentRow, newRow);

        newRow.querySelector('button[name="upButton"]').disabled = false;
        newRow.querySelector('button[name="downButton"]').disabled = false;
        button.disabled = false;
        button.nextElementSibling.disabled = false;

        deactivateButtons(tableName);
        changePositionOfUser(currentRow, -1);
        changePositionOfUser(newRow, 1);

    }

    function down(button, tableName)
    {
        var currentRow = button.parentNode.parentNode;
        var table = document.getElementById(tableName);
        var rows = table.getElementsByTagName('tr');
        var newRow = rows[currentRow.rowIndex + 1];
        table.insertBefore(currentRow, newRow);

        var upperRow = currentRow.previousElementSibling;
        upperRow.querySelector('button[name="upButton"]').disabled = false;
        upperRow.querySelector('button[name="downButton"]').disabled = false;
        button.disabled = false;
        button.previousElementSibling.disabled = false;

        deactivateButtons(tableName);

        changePositionOfUser(currentRow, 1);
        changePositionOfUser(upperRow, -1);
    }

    function changePositionOfUser(element, value)
    {
        var newValue = parseInt(element.getAttribute("position")) + value;
            $.ajax({
                type: "POST",
                url: "/mawaChangeUserPosition/" + element.getAttribute("id"),
                dataType:"json",
                data: {
                    position:newValue,
                    _token:"{{ csrf_token() }}",
                },
                success: function(data)
                {
                    element.setAttribute("position", newValue);
                }
            });
    }

    function deactivateButtons(tableName)
    {
        var tableBody = $('#' + tableName);
        var rows = tableBody.find('tr');
        var upButton = rows.eq(0).find('button[name="upButton"]');
        var downButton = rows.eq(-2).find('button[name="downButton"]');

        upButton.prop('disabled', true);
        downButton.prop('disabled', true);
    }
    window.onload = function()
    {
        deactivateButtons("permitTable");
    };



    setTypeaheadForUsers();

    var userURLTypeahead = "{{ route('mawa.user.search') }}?query=%QUERY%";
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
        userURLTypeahead = "{{ route('mawa.user.search') }}?query=%QUERY%";
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
            addUserElement(data);
        })
    }

    function addUserElement(data)
    {
        if($("#alertUserId").length)
        {
            $("#alertUserId").remove();
        }
        if($("#permitTable [userid='" + data["id"] + "']").length)
        {
            $("#alertUserId").remove();
            $("#alertDivUser").append($("<div>").addClass("alert alert-danger alert-dismissible").attr("id", "alertUserId").text("@lang('main.theUserHasAlreadyBeenAdded')"));
            $("#alertUserId").append('<a data-dismiss="alert" aria-label="close" href="#" class="close">&times;</a>');
            window.setTimeout(function() {
                $(".alert").fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove();
                });
            }, 4000);
        }
        else
        {
            var lastRow = $("#permitTable").find('tr').eq(-2);
            if(lastRow.length)
            {
                var positionRow = parseInt(lastRow.attr("position")) + 1;
            }
            else
            {
                var positionRow = 1;
            }
            $.ajax({
                type: "POST",
                url: "{{ route('mawa.add.user') }}",
                dataType:"json",
                data: {
                    areapermissionID:{{  $areapermissions->id }},
                    userID:data["id"],
                    position:positionRow,
                    _token:"{{ csrf_token() }}",
                },
                success: function(dataAreaPermission)
                {
                    lastRow.find('button[name="upButton"]').prop('disabled', false);
                    lastRow.find('button[name="downButton"]').prop('disabled', false);

                    var tableRow = $("<tr>").attr("id", dataAreaPermission["id"]).attr("userID", dataAreaPermission["userID"]).attr("position", dataAreaPermission["position"]);
                    tableRow.insertBefore("#addUserBevorRow");

                    var td1 = $('<td>').attr("nowrap", 'nowrap').addClass('text-right');
                    var td2 = $('<td>').text(data["forename"] + " " + data["surname"]);
                    var td3 = $('<td>').attr("nowrap", 'nowrap').addClass('text-right');

                    var buttonUp = $('<button>').addClass("btn btn-sm btn-outline-primary icon-arrow-up align-right").attr("type", "button").attr("name", "upButton").attr("id", "upButton-" + dataAreaPermission["id"]);
                    var buttonDown = $('<button>').addClass("btn btn-sm btn-outline-primary icon-arrow-down align-right").attr("type", "button").attr("disabled", "disabled").attr("name", "downButton").attr("id", "downButton-" + dataAreaPermission["id"]);

                    var buttonDelete = $('<button>').addClass("btn btn-outline-danger fa-trash-alt fa").attr("type", "button").attr("id", "deleteButton-" + dataAreaPermission["id"]);

                    td1.append(buttonUp, " ", buttonDown);
                    td3.append(buttonDelete);
                    tableRow.append(td1, td2, td3);
                    //after insert müssen die Buttons darüber wieder aktiv werden

                    $("#upButton-" + dataAreaPermission["id"]).click(function() {
                        up($("#upButton-" + dataAreaPermission["id"])[0], "permitTable");
                    });
                    $("#downButton-" + dataAreaPermission["id"]).click(function() {
                        down($("#downButton-" + dataAreaPermission["id"])[0], "permitTable");
                    });
                    $("#deleteButton-" + dataAreaPermission["id"]).click(function(e)
                    {
                        removeUser(dataAreaPermission["id"]);
                    });
                }
            });

        }
    }

        function removeUser(id)
        {
            $.ajax({
                type: "GET",
                url: "/mawaRemoveUser/" + id,
                success: function(data)
                {
                    $("#" + id).remove();
                    deactivateButtons("permitTable");
                }
            });
        }


</script>

@if(isset($success) && !empty($success))
    @if ($success == "savedSuccess")
    <script>
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 4000);
    </script>
    @endif
@endif

@endsection
