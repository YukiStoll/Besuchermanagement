@extends('layouts.layout')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
            <div class="card-header">
                <div class="form-row">
                    <div class="col-10">
                        <h1>
                            @lang('main.faq-help')
                        </h1>
                    </div>
                </div>
            </div>
                <div class="card-body">
                    <table class="table table-hover table-striped ">
                        <thead @if(env("APP_table_Color")) class="table-dark" style="background: {{ env("APP_table_Color") }}" @else class="thead-dark" @endif>
                            <tr>
                                <th scope="col">@sortablelink('surname', __('main.name'))</th>
                                <th scope="col">@sortablelink('email', __('main.email'))</th>
                            </tr>
                        </thead>
                        <tbody class="table-bordered">
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{$user->surname}}, {{$user->forename}}</td>
                                    <td>{{$user->email}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
