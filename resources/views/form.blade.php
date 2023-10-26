@extends('layouts.layout')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ $form['title'] }}</div>
                    <div class="card-body">
                        <form method="{{ $form['route']['method'] }}" action="{{ route($form['route']['route'])}}">
                            @foreach($form as $item)
                                @if($item != $form['route'] && $item != $form['button'] && $item != $form['title'])
                                @if(isset($item['inline']))
                                    <div class="form-row">
                                        @foreach($item as $dataset)
                                            @if(isset($dataset['id']))

                                                <div class="form-group col">
                                                    @if(isset($dataset['label']))
                                                        <label class="form-inline">{{ $dataset['label'] }}</label>
                                                    @endif
                                                    @if(empty($dataset['select']))
                                                        <input id="{{ $dataset['id'] }}" type="{{ $dataset['type'] }}" class="form-control" name="{{ $dataset['id'] }}" value="{{ $dataset['value'] }}" @isset($dataset['datalist']) list="datalist" @endisset{{ $dataset['required'] }} placeholder="{{ $dataset['text'] }}" autofocus>
                                                        @isset($dataset['datalist'])
                                                            <datalist id="datalist">
                                                                @foreach($dataset['datalist'] as $data)
                                                                    @foreach($data as $dataitem)
                                                                        <option value="{{ $dataitem }}"></option>
                                                                    @endforeach
                                                                @endforeach
                                                            </datalist>
                                                        @endisset
                                                    @else
                                                        <select name="{{ $dataset['id'] }}" class="form-control">
                                                            @foreach($dataset['select'] as $data)
                                                                <option value="{{ $data }}">{{ $data }}</option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <div class="form-row">

                                        <div class="form-group col">
                                            @if(isset($item['label']))
                                                <label class="form-inline">{{ $item['label'] }}</label>
                                            @endif
                                            @if(empty($item['select']))
                                                <input id="{{ $item['id'] }}" type="{{ $item['type'] }}" class="form-control" name="{{ $item['id'] }}" value="{{ $item['value'] }}" @isset($item['datalist']) list="datalist" @endisset{{ $item['required'] }} placeholder="{{ $item['text'] }}" autofocus>
                                                @isset($item['datalist'])
                                                    <datalist id="datalist">
                                                        @foreach($item['datalist'] as $data)
                                                            @foreach($data as $dataitem)
                                                                <option value="{{ $dataitem }}"></option>
                                                            @endforeach
                                                        @endforeach
                                                    </datalist>
                                                @endisset
                                            @else
                                                <select class="form-control" name="{{ $item['id'] }}">
                                                    @foreach($item['select'] as $data)
                                                        <option value="{{ $data }}">{{ $data }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                @endif
                            @endforeach
                            @csrf
                            <div class="form-row">
                                <div class="form-group col">
                                    <button type="submit" class="btn btn-primary form-control">{{ $form['button'] }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
