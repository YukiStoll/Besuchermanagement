@extends('layouts.layout')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="form-row">
                        <div class="col">
                            <form method="POST" enctype="multipart/form-data" action="{{ route('admin-settings.update') }}">

                                <div class="form-row">
                                    <div class="form-group col">
                                        <div class="form-group col">
                                            <label class="label">@lang('main.roomOccupancyFiles'): </label>
                                        </div>
                                    </div>
                                    <div class="form-group col">
                                        <input type="text" class="form-control" name="room_occupancy_file" id="room_occupancy_file" value="{{ $admin_setting[0]->setting_value }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col">
                                        <div class="form-group col">
                                            <label class="label">@lang('main.canteenEMail'): </label>
                                        </div>
                                    </div>
                                    <div class="form-group col">
                                        <input type="text" class="form-control" name="canteenEMail" id="canteenEMail" value="{{ $admin_setting[1]->setting_value }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col">
                                        <a target="_blank" rel="noopener" href="{{ URL::to("documents\Anfahrskizze Unilever Heppenheim.pdf") }}">@lang('main.roadmap')</a>
                                    </div>
                                    <div class="form-group col">
                                        <label style="height: 38px" class="btn btn-full btn-primary text-light" id="ro" for="roadmap">Upload @lang('main.roadmap')</label>
                                        <input class="invisible" accept="application/pdf" type="file" name="roadmap"  id="roadmap">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col">
                                        <a target="_blank" rel="noopener" href="{{ URL::to("documents\Hygienevorschriften Fremdfirmen -Deutsch.pdf") }}">@lang('main.hygieneRegulationsForExternalCompaniesDE')</a>
                                    </div>
                                    <div class="form-group col">
                                        <label style="height: 38px" class="btn btn-full btn-primary text-light" id="hygieneRegulationsDE" for="hyDE">Upload @lang('main.hygieneRegulationsForExternalCompaniesDE')</label>
                                        <input class="invisible" accept="application/pdf" type="file" name="hygieneRegulationsDE"  id="hyDE">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col">
                                        <a target="_blank" rel="noopener" href="{{ URL::to("documents\Hygienevorschriften Fremdfirmen -Englisch.pdf") }}">@lang('main.hygieneRegulationsForExternalCompaniesENG')</a>
                                    </div>
                                    <div class="form-group col">
                                        <label style="height: 38px" class="btn btn-full btn-primary text-light" id="hygieneRegulationsENG" for="hyENG">Upload @lang('main.hygieneRegulationsForExternalCompaniesENG')</label>
                                        <input class="invisible" accept="application/pdf" type="file" name="hygieneRegulationsENG"  id="hyENG">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col">
                                        <a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\00 Allgemeine Arbeitserlaubnis.pdf") }}">00 Allgemeine Arbeitserlaubnis</a>
                                    </div>
                                    <div class="form-group col">
                                        <label style="height: 38px" class="btn btn-full btn-primary text-light" id="00" for="00 Allgemeine Arbeitserlaubnis">Upload @lang('main.workPermissionDocuments')</label>
                                        <input class="invisible" accept="application/pdf" type="file" name="00 Allgemeine Arbeitserlaubnis"  id="00 Allgemeine Arbeitserlaubnis">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col">
                                        <a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\01 spez. Arbeitserlaubnis Feuer und Schweißen.pdf") }}">01 spez. Arbeitserlaubnis Feuer und Schweißen</a>
                                    </div>
                                    <div class="form-group col">
                                        <label style="height: 38px" class="btn btn-full btn-primary text-light" id="01" for="01 spez. Arbeitserlaubnis Feuer und Schweißen">Upload @lang('main.workPermissionDocuments')</label>
                                        <input class="invisible" accept="application/pdf" type="file" name="01 spez. Arbeitserlaubnis Feuer und Schweißen"  id="01 spez. Arbeitserlaubnis Feuer und Schweißen">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col">
                                        <a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\02 spez. Arbeitserlaubnis Höhe.pdf") }}">02 spez. Arbeitserlaubnis Höhe</a>
                                    </div>
                                    <div class="form-group col">
                                        <label style="height: 38px" class="btn btn-full btn-primary text-light" id="02" for="02 spez. Arbeitserlaubnis Höhe">Upload @lang('main.workPermissionDocuments')</label>
                                        <input class="invisible" accept="application/pdf" type="file" name="02 spez. Arbeitserlaubnis Höhe"  id="02 spez. Arbeitserlaubnis Höhe">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col">
                                        <a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\03 spez. Arbeitserlaubnis Behälter.pdf") }}">03 spez. Arbeitserlaubnis Behälter</a>
                                    </div>
                                    <div class="form-group col">
                                        <label style="height: 38px" class="btn btn-full btn-primary text-light" id="03" for="03 spez. Arbeitserlaubnis Behälter">Upload @lang('main.workPermissionDocuments')</label>
                                        <input class="invisible" accept="application/pdf" type="file" name="03 spez. Arbeitserlaubnis Behälter"  id="03 spez. Arbeitserlaubnis Behälter">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col">
                                        <a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\04 spez. Arbeitserlaubnis Erdarbeiten.pdf") }}">04 spez. Arbeitserlaubnis Erdarbeiten</a>
                                    </div>
                                    <div class="form-group col">
                                        <label style="height: 38px" class="btn btn-full btn-primary text-light" id="04" for="04 spez. Arbeitserlaubnis Erdarbeiten">Upload @lang('main.workPermissionDocuments')</label>
                                        <input class="invisible" accept="application/pdf" type="file" name="04 spez. Arbeitserlaubnis Erdarbeiten"  id="04 spez. Arbeitserlaubnis Erdarbeiten">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col">
                                        <a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\05 spez. Arbeitserlaubnis Ammoniak.pdf") }}">05 spez. Arbeitserlaubnis Ammoniak</a>
                                    </div>
                                    <div class="form-group col">
                                        <label style="height: 38px" class="btn btn-full btn-primary text-light" id="05" for="05 spez. Arbeitserlaubnis Ammoniak">Upload @lang('main.workPermissionDocuments')</label>
                                        <input class="invisible" accept="application/pdf" type="file" name="05 spez. Arbeitserlaubnis Ammoniak"  id="05 spez. Arbeitserlaubnis Ammoniak">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col">
                                        <a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\06 spez. Arbeitserlaubnis Öffnen von Systemen.pdf") }}">06 spez. Arbeitserlaubnis Öffnen von Systemen</a>
                                    </div>
                                    <div class="form-group col">
                                        <label style="height: 38px" class="btn btn-full btn-primary text-light" id="06" for="06 spez. Arbeitserlaubnis Öffnen von Systemen">Upload @lang('main.workPermissionDocuments')</label>
                                        <input class="invisible" accept="application/pdf" type="file" name="06 spez. Arbeitserlaubnis Öffnen von Systemen"  id="06 spez. Arbeitserlaubnis Öffnen von Systemen">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col">
                                        <a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\07 spez. Arbeitserlaubnis Kran.pdf") }}">07 spez. Arbeitserlaubnis Kran</a>
                                    </div>
                                    <div class="form-group col">
                                        <label style="height: 38px" class="btn btn-full btn-primary text-light" id="07" for="07 spez. Arbeitserlaubnis Kran">Upload @lang('main.workPermissionDocuments')</label>
                                        <input class="invisible" accept="application/pdf" type="file" name="07 spez. Arbeitserlaubnis Kran"  id="07 spez. Arbeitserlaubnis Kran">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col">
                                        <a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\08 spez. Arbeitserlaubnis Spannung.pdf") }}">08 spez. Arbeitserlaubnis Spannung</a>
                                    </div>
                                    <div class="form-group col">
                                        <label style="height: 38px" class="btn btn-full btn-primary text-light" id="08" for="08 spez. Arbeitserlaubnis Spannung">Upload @lang('main.workPermissionDocuments')</label>
                                        <input class="invisible" accept="application/pdf" type="file" name="08 spez. Arbeitserlaubnis Spannung"  id="08 spez. Arbeitserlaubnis Spannung">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col">
                                        <a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\09 spez. Arbeitserlaubnis Heißwasserkessel.pdf") }}">09 spez. Arbeitserlaubnis Heißwasserkessel</a>
                                    </div>
                                    <div class="form-group col">
                                        <label style="height: 38px" class="btn btn-full btn-primary text-light" id="09" for="09 spez. Arbeitserlaubnis Heißwasserkessel">Upload @lang('main.workPermissionDocuments')</label>
                                        <input class="invisible" accept="application/pdf" type="file" name="09 spez. Arbeitserlaubnis Heißwasserkessel"  id="09 spez. Arbeitserlaubnis Heißwasserkessel">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col">
                                        <a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\10 spez. Arbeitserlaubnis Gefriertunnel Tippbetrieb.pdf") }}">10 spez. Arbeitserlaubnis Gefriertunnel Tippbetrieb</a>
                                    </div>
                                    <div class="form-group col">
                                        <label style="height: 38px" class="btn btn-full btn-primary text-light" id="09" for="10 spez. Arbeitserlaubnis Gefriertunnel Tippbetrieb">Upload @lang('main.workPermissionDocuments')</label>
                                        <input class="invisible" accept="application/pdf" type="file" name="10 spez. Arbeitserlaubnis Gefriertunnel Tippbetrieb"  id="10 spez. Arbeitserlaubnis Gefriertunnel Tippbetrieb">
                                    </div>
                                </div>

                                @csrf
                                <div class="form-row">
                                    <div class="form-group col">
                                        <button class="btn btn-outline-dark w-100 saveButton">@lang("main.save")</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
    <script>
        $('input').change(function () {
            if( $("#" + $(this).attr("id").substr(0, 2)).length > 0)
            {
                $("#" + $(this).attr("id").substr(0, 2)).addClass("btn-warning");
            }
            else
            {
                $("#" + $(this).attr("name")).addClass("btn-warning");
            }

            console.log($(this));
        });
    </script>
@endsection
