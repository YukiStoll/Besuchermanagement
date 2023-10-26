@extends('layouts.layout')
@section('content')
    @if(isset($questionsSafetyInstructions['languages']))
        <div class="row">
            <div class="col-3">
            </div>
            <div class="input-group col-6">
                <select class="form-control w-auto @error('entryPermission') is-invalid @enderror" id="entryPermission" name="entryPermission" autofocus>
                    <option selected disabled>@lang('main.languageSelectionForTestResults')</option>
                    @foreach($questionsSafetyInstructions['languages'] as $languages)
                        <option value="{{ $languages['short'] }}">{{ $languages['name'] }}</option>
                    @endforeach
                </select>
                <span> </span>
                <button type="button" onclick="printTestResults()" class="btn btn-outline-secondary fa fa-print"></button>
            </div>
        </div>
        <div class="row">

        </div>
        <div class="row mt-3 justify-content-center">
            <div class="col-1">
            </div>
            <div id="main" class="card col">

            </div>
            <div class="col-1">
            </div>
        </div>
    @else
        <div class="row mt-3 justify-content-center">
            <div class="col-1">
            </div>
            <div class="card col">
                <div class="card-body">
                    <h2>@lang('main.noQuestionsAvailable')</h2>
                    <button class="btn btn-outline-primary" onclick="window.close( );" type="button">@lang('main.close')</button>
                </div>
            </div>
            <div class="col-1">
            </div>
        </div>
    @endif
@endsection
@section('scripts')
    <script>
        function printTestResults()
        {
            var win = window.open("/printTestResults/{{ $id }}/" + $('select').children("option:selected").val(), '_blank');
            win.focus();
        }
    </script>
    <script>

        $('select').change(function () {
            var short = $(this).children("option:selected").val();
            var testResult = @json($questionsSafetyInstructions['testResult']);
            $('#main').empty();
            $.each(testResult['rounds'], function(k, v){
                console.log(v);
                var divCardHeader = $("<div>").attr('class', "card-header");
                if(short == "de")
                {
                    var h2 = $("<h2>").text("Testdurchlauf " + v['id']);
                }
                else
                {
                    var h2 = $("<h2>").text("Test run " + v['id']);
                }
                $('#main').append(divCardHeader);
                divCardHeader.append(h2);
                var divCardBody = $("<div>").attr('class', "card-body");
                $('#main').append(divCardBody);
                $.each(v['wrongAnsweredQuestions'], function(kv, vv){
                    $.each(vv['questionInAllLanguages'], function(kvv, vvv){
                        if(vvv['languageShort'] == short)
                        {
                            var div = $("<div>");
                            var hr = $("<hr>");
                            divCardBody.append(div);
                            divCardBody.append(hr);
                            var h4 = $("<h4>").text(vvv['question']);
                            div.append(h4);
                            $.each(vvv['answers'], function(kvvv, vvvv){
                                var spann3 = "<span> </span>";
                                if(vvvv['id'] == vvv['correctAnswerID'])
                                {
                                    var spann1 = "<span class='fa fa-check'></span>";
                                    var pn1 = $("<p>").html(spann1 + spann3 + vvvv['answer']).attr("class","text-success");
                                    div.append(pn1);
                                }
                                else if(vvvv['id'] == vvv['wrongAnsweredID'])
                                {
                                    var spann2 = "<span class='fa fa-times'></span>";
                                    var pn2 = $("<p>").html(spann2 + spann3 + vvvv['answer']).attr("class","text-danger");
                                    div.append(pn2);
                                }
                                else
                                {
                                    var pn3 = $("<p>").text(vvvv['answer']);
                                    div.append(pn3);
                                }
                            });
                        }
                    });
                });
            });
        });

    </script>
@endsection
