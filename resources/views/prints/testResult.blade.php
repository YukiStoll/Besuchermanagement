<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('layouts.header')
<style type="text/css" media="print">
    @page {
        size: auto;
        margin: 0;
    }
</style>
    <body onload="window.print()">
        <div class="row col mt-3 justify-content-center">
            <div class="col-1" id="test">
            </div>
            <div id="main" class="col print">

            </div>
            <div class="col-2">
            </div>
        </div>
        @include('layouts.bottomScripts')
        <script>
            const length = 1450;
            let counter = 0;
            var short = "{{ $short }}";
            var testResult = @json($questionsSafetyInstructions['testResult']);
            $('#main').empty();
            $.each(testResult['rounds'], function(k, v){
                var divCardHeader = $("<div>").attr('class', " row").attr('id', counter);
                if(short == "de")
                {
                    var h2 = $("<h2>").text("Testdurchlauf " + v['id']);
                }
                else
                {
                    var h2 = $("<h2>").text("Test run " + v['id']);
                }
                var coldiv1 = $("<div>").attr("class","col-4");
                var coldiv2 = $("<div>").attr("class","col");
                var coldiv3 = $("<div>").attr("class","col-auto").attr("id","name");
                $('#main').append(divCardHeader);
                coldiv1.append(h2);
                divCardHeader.append(coldiv1, coldiv2, coldiv3);
                var h21 = $("<h2>").text("{{ $visitor['forename'] }} {{ $visitor['surname'] }}");
                coldiv3.append(h21);
                var divCardBody = $("<div>").attr('class', "").attr("id","CardBody" + counter);
                counter++;
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
            var divFooterCardBody = $('<div>').attr("class"," mt-5");
            var divFooterRow1 = $('<div>').attr("class","row");
            var divFooterRow2 = $('<div>').attr("class","row");
            var divFooter11 = $('<div>').attr("class","col");
            var divFooter21 = $('<div>').attr("class","col");
            var divFooter12 = $('<div>').attr("class","col");
            var divFooter22 = $('<div>').attr("class","col");
            var hr1 = $("<hr>");
            var hr2 = $("<hr>");
            if(short == "de")
            {
                var p1 = $("<p>").text("Unterschrift Mitarbeiter, Datum");
                var p2 = $("<p>").text("Unterschrift Besucher, Datum");
            }
            else
            {
                var p1 = $("<p>").text("Signature of employee, date");
                var p2 = $("<p>").text("Signature of visitor, date");
            }
            $('#main').append(divFooterCardBody);
            divFooterCardBody.append(divFooterRow1);
            divFooterRow1.append(divFooter11, divFooter21);
            divFooter11.append(hr1);
            divFooter21.append(hr2);
            divFooterCardBody.append(divFooterRow2);
            divFooterRow2.append(divFooter12, divFooter22);
            divFooter12.append(p1);
            divFooter22.append(p2);
            seperate();

            function seperate()
            {
                let heightModifier = 1;
                for(let i = 0; i < counter; i++)
                {
                    let br = [$("<br>")];
                    let cardBody = $('#CardBody' + i);
                    let cardHeader = $('#' + i);
                    let height = cardBody.outerHeight() + cardHeader.outerHeight();

                    if(height < length)
                    {
                        for(let w = 0;w < 100; w++)
                        {
                            if($('#' + (i + 1)).length && $('#' + (i + 1)).position()['top'] < length * (i + heightModifier))
                            {
                                br.push($("<br>"));
                                cardBody.append(br[w]);
                            }
                            else
                            {
                                heightModifier = heightModifier - 0.05;
                                break;
                            }
                        }
                    }
                }
            }
        </script>

    </body>
</html>
