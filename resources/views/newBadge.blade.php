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
                        <h3>@lang('main.newBadge')</h3>

                        <form method="POST" action="">

                            <div class="form-row">
                                <div class="form-group col">
                                    <label>@lang('main.surname')</label>
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" autofocus>
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group col">
                                    <label>@lang('main.forename')</label>
                                    <input id="forename" type="text" class="form-control @error('forename') is-invalid @enderror" name="forename" value="{{ old('forename') }}" autofocus>
                                    @error('forename')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-row">
                                <div class="form-group col">
                                    <label>@lang('main.cardID')</label>
                                    <input id="cardID" type="text" class="form-control @error('cardID') is-invalid @enderror" name="cardID" value="{{ old('cardID') }}" autofocus>
                                    @error('cardID')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group col">
                                    <label>@lang('main.mawaType')</label>
                                    <input id="type" type="text" class="form-control @error('type') is-invalid @enderror" name="type" value="{{ old('type') }}" autofocus>
                                    @error('type')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-row">
                                <div class="form-group col">
                                    <label>@lang('main.startDate')</label>
                                    <input id="startDate" type="date" class="form-control @error('startDate') is-invalid @enderror" name="startDate" value="{{  date('Y-m-d', strtotime(old('startDate') ? old('startDate') : date('Y-m-d'))) }}" autofocus>
                                    @error('startDate')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group col">
                                    <label>@lang('main.endDate')</label>
                                    <input id="endDate" type="date" class="form-control @error('endDate') is-invalid @enderror" name="endDate" value="{{  date('Y-m-d', strtotime(old('endDate') ? old('endDate') : date('Y-m-d'))) }}" autofocus>
                                    @error('endDate')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-row">
                                <div class="form-group col">
                                    <select id="mainAreaPermissionSelect[]" name="mainAreaPermissionSelect[]" class="form-control selectpicker @error('mainAreaPermissionSelect') is-invalid @enderror" data-live-search="true" multiple data-actions-box="true" title="@lang('main.doors')">
                                        @foreach ($areaPermissions as $areaPermission)
                                            <option @if(!empty(old('mainAreaPermissionSelect')) && in_array($areaPermission->id, old('mainAreaPermissionSelect'))) selected @endif value="{{ $areaPermission->id }}">{{$areaPermission->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
















                            @csrf

                            <div class="form-row">
                                <div class="form-group col">
                                    <button class="btn btn-outline-primary w-100 saveButton">@lang("main.save")</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </table>
        </div>
    </div>
</div>

@endsection
