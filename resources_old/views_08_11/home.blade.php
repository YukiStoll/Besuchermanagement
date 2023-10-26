@extends('layouts.layout')
@section('content')
    @if (session('success') && session('success') == 1)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            @lang('main.successEntryPermission')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @elseif(session('success') && session('success') == 2)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @lang('main.dangerEntryPermission')
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
    @elseif(session('successWorkPermission') && session('successWorkPermission') == 2)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @lang('main.dangerWorkPermission')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <h1>@lang('main.welcome') {{ Auth::user()->name }}</h1>
@endsection

