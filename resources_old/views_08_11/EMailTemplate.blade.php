@extends('layouts.layout')
@section('content')

@isset($saved)
    @if($saved === true)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            @lang('email.success')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @else
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @lang('email.failed')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
@endisset


<div class="d-flex justify-content-end">
    <button class="btn btn-outline-primary" data-target="#Legende" data-toggle="modal" type="button"><span class="fa fa-question"></span></button>
</div>



<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
            <div class="card-header">
                <div class="form-row">
                    <div class="col-10">
                        <h1>
                            @if($id == 1)
                                @lang('main.visitorEMail')
                            @elseif($id == 2)
                                @lang('main.employeeEMail')
                            @elseif($id == 3)
                                @lang('main.canteenEMail')
                            @elseif($id == 4)
                                @lang('main.entrancePermissionEMail')
                            @elseif($id == 5)
                                @lang('main.workPermissionEMail')
                            @elseif($id == 6)
                                @lang('main.notificationOfApproval')
                            @elseif($id == 7)
                                @lang('main.visitorArrivalNotice')
                            @endif
                        </h1>
                    </div>
                    <div class="col">
                        <select class="form-control" id="language" name="language">
                            <option @if(old('language') == "german" || $language == "german") selected @endif value="german">@lang('main.german')</option>
                            <option @if(old('language') == "english" || $language == "english") selected @endif value="english">@lang('main.english')</option>
                        </select>
                    </div>
                </div>
            </div>
                <div class="card-body">
                    <form method="post" action="{{ route('emailTemplatesPost') }}?id={{ $id }}&language={{ $language }}">
                        <textarea id="summernote" name="content">{!! $detail !!}</textarea>
                        @csrf
                        <button class="form-control btn btn-outline-primary saveButton" type="submit">@lang('main.save')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="Legende" tabindex="-1" role="dialog" aria-labelledby="EditlLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1>E-Mail - Parameter</h1>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>@lang('email.legendHeader')</p>
                    @include('legende')
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
<link href="summernote-0.8.12-dist\dist\summernote-bs4.css" rel="stylesheet">
<script src="summernote-0.8.12-dist\dist\summernote-bs4.min.js"></script>
<script>
    $( "#language" ).change(function() {
        window.location.replace("{{ route('emailTemplates') }}" + "?id=" + "{{ $id }}" + "&language=" + $("#language").val());
    });

window.setTimeout(function() {
                        $(".alert").fadeTo(500, 0).slideUp(500, function(){
                            $(this).remove();
                        });
                    }, 5000);
$(document).ready(function() {
    $('#summernote').summernote({
        height: 600,
        });
});



</script>
@endsection
