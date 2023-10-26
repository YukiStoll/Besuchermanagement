@extends('layouts.layout')
@section('content')
    <button class="btn btn-outline-primary" data-toggle="modal" data-target="#test" data-dismiss="modal" type="button" onclick="create()">MaWa Create</button>
    <button class="btn btn-outline-primary" type="button" onclick="destroy()">MaWa Destroy</button>
    <button class="btn btn-outline-primary" type="button" onclick="store()">MaWa Store</button>
@endsection
@section('scripts')
    <script>
        function create()
        {
            $.ajax
            ({
                type: "post",
                url: "/api/MaWa-Badge/" + "0022803",
                success: function(data)
                {
                    console.log(data);
                },
                error: function (error) {
                    console.log(error);
                }

            });
        }

        function destroy()
        {
            $.ajax
            ({
                type: "delete",
                url: "/api/MaWa-Badge/" + "0022803",
                success: function(data)
                {
                    console.log(data);
                },
                error: function (error) {
                    console.log(error);
                }

            });
        }
        function store()
        {
            $.ajax
            ({
                type: "put",
                url: "{{ route('MaWa.store') }}",
                data: {
                    visitID:"12345678",
                    visitor:{
                        id:"1",
                        forename:"Jeremias",
                        surname:"Stoll",
                    },
                    badge_number:"0022803",
                    dates:{
                        startDate:"31.01.2020 12:00",
                        endDate:"31.01.2020 13:00",
                    },
                },
                success: function(data)
                {
                    console.log(data);
                },
                error: function (error) {
                    console.log(error);
                }

            });
        }
    </script>
@endsection
