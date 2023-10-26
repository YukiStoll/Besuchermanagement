<form id="makeVisitForm" method="POST">
    <div class="form-row">
        <div class="form-group col">
            <label class="form-inline">@lang('main.startDate')</label>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <input id="makeVisitstartDate" type="date" class="form-control" name="startDate" disabled autofocus>
        </div>
        <div class="form-group col">
            <input type="time" class="form-control" id="makeVisitstartTime"  name="startTime" disabled autofocus>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <label class="form-inline">@lang('main.endDate')</label>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <input type="date" class="form-control" id="makeVisitendDate" name="endDate" disabled autofocus>
        </div>
        <div class="form-group col">
            <input type="time" class="form-control" id="makeVisitendTime" name="endTime" disabled autofocus>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <label class="form-inline">@lang('main.employee')</label>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <input disabled  type="text" class="form-control" id="makeVisitemployee" name="employee" value="">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <label class="form-inline">@lang('main.visitors')</label>
        </div>
    </div>
    <div id="visitors">
    </div>
    <input type="hidden" id="id">


    <div class="form-row">
        <div class="form-group col-3 text-right">
            <label class="col-form-label-lg">@lang('main.reasonForVisit'):</label>
        </div>
        <div class="form-group col">
            <input id="makeVisitreasonForVisit" type="text" class="form-control @error('makeVisitreasonForVisit') is-invalid @enderror" name="makeVisitreasonForVisit" value="{{ old('makeVisitreasonForVisit') }}" placeholder="@lang('main.reasonForVisit')">
            @error('makeVisitreasonForVisit')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col">
            <label class="form-inline">@lang('main.contactPossibility')</label>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <select class="form-control" id="makeVisitcontactPossibility" disabled name="contactPossibility" autofocus>
                <option value="E-Mail">@lang('main.email')</option>
                <option value="Telefon">@lang('main.phone')</option>
                <option value="SMS">@lang('main.sms')</option>
            </select>
        </div>
    </div>
</form>
@section('contentSscripts')
 <script>
     $.ajax({
            type: "POST",
            url: "{{ route('searchAdvancedRegistration.search') }}",
            dataType:"json",
            data:
                {
                    visitIDsearch:"{{ $requestData['visitIDsearch'] }}",
                    _token:"{{ csrf_token() }}",
                },
            success: function(data)
            {
                $('#makeVisit').modal('show');
                document.getElementById('makeVisitButton').onclick = function ()
                {
                    sendNewVisit(data[0]["id"])
                };

                var startd = new Date(data[0]['startDate']);
                var stYear = startd.getFullYear();
                if(startd.getDate() > 9)
                {
                    var stDate = startd.getDate();
                }
                else
                {
                    var stDate = "0" + startd.getDate();
                }
                if((1 + startd.getUTCMonth()) > 9)
                {
                    var stMotnh = (1 + startd.getMonth());
                }
                else
                {
                    var stMotnh = "0"+ (1 + startd.getMonth());
                }
                if(startd.getHours() > 9)
                {
                    var stHours = startd.getHours();
                }
                else
                {
                    var stHours = "0" + startd.getHours();
                }
                if(startd.getMinutes() > 9)
                {
                    var stminuts = startd.getMinutes();
                }
                else
                {
                    var stminuts = "0"+ startd.getMinutes();
                }

                var endd = new Date(data[0]['endDate']);
                var endYear = endd.getFullYear();
                if(endd.getDate() > 9)
                {
                    var endDate = endd.getDate();
                }
                else
                {
                    var endDate = "0" + endd.getDate();
                }
                if((1 + endd.getMonth()) > 9)
                {
                    var endMotnh = (1 + endd.getMonth());
                }
                else
                {
                    var endMotnh = "0"+ (1 + endd.getMonth());
                }
                if(endd.getHours() > 9)
                {
                    var endHours = endd.getHours();
                }
                else
                {
                    var endHours = "0" + endd.getHours();
                }
                if( endd.getMinutes() > 9)
                {
                    var endminuts = endd.getMinutes();
                }
                else
                {
                    var endminuts = "0"+ endd.getMinutes();
                }
                console.log(data);
                console.log(data[0]['reasonForVisit']);
                $('#makeVisitstartDate').val(stYear + "-" + stMotnh + "-" + stDate);
                $("#makeVisitstartTime").val(stHours + ":" + stminuts);
                $("#makeVisitendDate").val(endYear + "-" + endMotnh + "-" + endDate);
                $("#makeVisitendTime").val(endHours + ":" + endminuts);
                $("#makeVisitreasonForVisit").val(data[0]['reasonForVisit']);
                $("#makeVisitroadmap" + data[0]['roadmap']).attr('checked', 'checked');
                $("#makeVisitcontactPossibility").val(data[0]['contactPossibility']);
                $("#id").val(data[0]['id']);
                var allocationid = data[0]['allocationid'];
                $.ajax
                ({
                    type: "POST",
                    url: "{{ route('myAdvanceRegistrationGetVisitors') }}",
                    dataType:"json",
                    data:
                        {
                            allocationid:data[0]['allocationid'],
                            _token:"{{ csrf_token() }}",
                        },
                    success: function(data)
                    {
                        $("#childDiv").empty();
                        $.each(data['mainVisitors'], function(k, v) {
                            $.each(v, function(kv, vv) {
                                addvisitorElement(vv, allocationid, "visitors");
                            });
                        });
                        $.each(data['groupVisitors'], function(k, v) {
                            addGroupMember(v, "visitors");
                        });
                    },
                    error: function (error) {
                        console.log(error);
                    }

                });


                $('#makeVisitemployee').val(data[1]['forename'] + ' ' + data[1]['surname']);
            },
                error: function (error) {
                    console.log(error);
                }

        });

        function sendNewVisit(id)
        {
            $.ajax
            ({
                type: "POST",
                url: "{{ route('newVisit.store') }}",
                dataType:"json",
                data:
                    {
                        id:id,
                        _token:"{{ csrf_token() }}",
                    },
                success: function(data)
                {
                    window.location.replace( '{!! Request::fullurl() !!}' + '&success=true' );
                },
                error: function (error) {
                    console.log(error);
                    window.location.replace( '{!! Request::fullurl() !!}' + '&success=false' );
                }

            });
        }

@isset($requestData['success'])
    @if($requestData['success'] == true)
        $("#visitsuccalert").remove();
        var trest = ($('<div>').addClass("alert alert-success alert-dismissible").attr("id", "visitsuccalert").text("@lang('main.successCreateVisitMessage')"));
        $("#successdiv").append(trest);
        trest.append('<a aria-label="close" data-dismiss="alert" href="#" class="close">&times;</a>');
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 10000);
    @endif
@endisset
 </script>
@endsection
