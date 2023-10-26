@extends('layouts.layout')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Nutzer Informationen Vervollständigen</div>

                    <div class="card-body">
                        <form method="POST" action="">

                            <div class="form-group row">
                                <label for="telephonenumber" class="col-md-4 col-form-label text-md-right">Telefon Nummer</label>

                                <div class="col-md-6">
                                    <input id="telephonenumber" type="text" class="form-control" name="telephonenumber" value="" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="department" class="col-md-4 col-form-label text-md-right">Abteilung</label>

                                <div class="col-md-6">
                                    <input id="department" type="text" class="form-control" name="department" required>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary saveButton">Vervollständigen</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
