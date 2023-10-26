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
                                        <a target="_blank" rel="noopener" href="{{ URL::to("documents\Anfahrskizze_Unilever_Heppenheim.pdf") }}">@lang('main.roadmap')</a>
                                    </div>
                                    <div class="form-group col">
                                        <label style="height: 38px" class="btn btn-full btn-primary text-light" id="AnfahrskizzeUnileverHeppenheimLabel" for="AnfahrskizzeUnileverHeppenheim">Upload @lang('main.roadmap')</label>
                                        <input class="invisible" accept="application/pdf" type="file" name="Anfahrskizze Unilever Heppenheim"  id="AnfahrskizzeUnileverHeppenheim">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col">
                                        <a target="_blank" rel="noopener" href="{{ URL::to("documents\Hygienevorschriften_Fremdfirmen_-Deutsch.pdf") }}">@lang('main.hygieneRegulationsForExternalCompaniesDE')</a>
                                    </div>
                                    <div class="form-group col">
                                        <label style="height: 38px" class="btn btn-full btn-primary text-light" id="HygienevorschriftenFremdfirmenDeutschLabel" for="HygienevorschriftenFremdfirmenDeutsch">Upload @lang('main.hygieneRegulationsForExternalCompaniesDE')</label>
                                        <input class="invisible" accept="application/pdf" type="file" name="Hygienevorschriften Fremdfirmen -Deutsch"  id="HygienevorschriftenFremdfirmenDeutsch">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col">
                                        <a target="_blank" rel="noopener" href="{{ URL::to("documents\Hygienevorschriften_Fremdfirmen_-Englisch.pdf") }}">@lang('main.hygieneRegulationsForExternalCompaniesENG')</a>
                                    </div>
                                    <div class="form-group col">
                                        <label style="height: 38px" class="btn btn-full btn-primary text-light" id="HygienevorschriftenFremdfirmenEnglischLabel" for="HygienevorschriftenFremdfirmenEnglisch">Upload @lang('main.hygieneRegulationsForExternalCompaniesENG')</label>
                                        <input class="invisible" accept="application/pdf" type="file" name="Hygienevorschriften Fremdfirmen -Englisch"  id="HygienevorschriftenFremdfirmenEnglisch">
                                    </div>
                                </div>




                                @foreach ($workPermissions as $workPermission)

                                    <div class="form-row">
                                        <div class="form-group col-6">
                                            <a target="_blank" rel="noopener" href="{{ URL::to($workPermission->setting_value) }}">{{ $workPermission->setting_key }}</a>
                                        </div>
                                        <div class="form-group col-3">
                                            <label style="height: 38px" class="btn btn-full btn-primary text-light"  id="{{ str_replace(" ", "", $workPermission->setting_key) }}Label" for="{{ str_replace(" ", "", $workPermission->setting_key) }}">Upload @lang('main.workPermissionDocument')</label>
                                            <input class="invisible" accept="application/pdf" type="file" name="workPermission_{{ $workPermission->setting_key }}"  id="{{ str_replace(" ", "", $workPermission->setting_key) }}">
                                        </div>
                                        <div class="form-group col-2">
                                            <button type="button" onclick="" class="btn btn-outline-danger fa-trash-alt fa"></button>
                                        </div>
                                    </div>

                                @endforeach



                                <div class="form-row">

                                    <div class="form-group col-6">
                                        <input type="text" class="form-control @error('newWorkPermissionName') is-invalid @enderror" name="newWorkPermissionName" id="newWorkPermissionName" value="{{ old('newWorkPermissionName') }}" placeholder="@lang('main.newWorkPermit')">
                                        @error('newWorkPermissionName')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-3">
                                        <label style="height: 38px" class="btn btn-full @error('newWorkPermissionFile') is-invalid btn-danger @enderror btn-primary text-light " id="newWorkPermissionFileLabel" for="newWorkPermissionFile">Upload @lang('main.workPermissionDocument')</label>
                                        <input class="invisible" accept="application/pdf" type="file" name="newWorkPermissionFile" id="newWorkPermissionFile">
                                        @error('newWorkPermissionFile')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
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
                console.log("test1");
            let button = $("#" + $(this).attr("id") + "Label");
            if( button.length > 0)
            {
                console.log("test2");
                button.removeClass("btn-primary btn-danger");
                button.addClass("btn-warning");
            }
                console.log("test3");
        });
    </script>
@endsection
