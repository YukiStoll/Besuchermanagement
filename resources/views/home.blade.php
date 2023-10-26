@extends('layouts.layout')
@section('content')
    @if (session('successEntryPermission') && session('successEntryPermission') == 1)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            @lang('main.successEntryPermission')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @elseif(session('successEntryPermission') && session('successEntryPermission') == 3)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            @lang('main.successEntryPermissionDenied')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @elseif(session('successEntryPermission') && session('successEntryPermission') == 2)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @lang('main.dangerEntryPermission')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @elseif(session('successEntryPermission') && session('successEntryPermission') == 4)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            @lang('main.oldEntryPermission')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('successWorkPermission') && session('successWorkPermission') == 1)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            @lang('main.successWorkPermission')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @elseif(session('successWorkPermission') && session('successWorkPermission') == 3)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            @lang('main.successWorkPermissionDenied')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @elseif(session('successWorkPermission') && session('successWorkPermission') == 2)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @lang('main.dangerWorkPermission')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @elseif(session('successWorkPermission') && session('successWorkPermission') == 4)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            @lang('main.oldWorkPermission')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('successAreaPermission') && session('successAreaPermission') == 1)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            @lang('main.successAreaPermission')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @elseif(session('successAreaPermission') && session('successAreaPermission') == 3)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            @lang('main.successAreaPermissionDenied')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @elseif(session('successAreaPermission') && session('successAreaPermission') == 2)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @lang('main.dangerAreaPermission')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @elseif(session('successAreaPermission') && session('successAreaPermission') == 4)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            @lang('main.oldAreaPermission')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <h1>@lang('main.welcome') {{ Auth::user()->name }}</h1>
@endsection

