<html>
    <body onload="window.print()">
        <p>{{ $visitor }}</p>
        <img src="data:image/png;base64,{!! base64_encode(QrCode::format('png')->size(100)->generate($id . '#')) !!}">
        <p>{{ $id }}</p>
    </body>
</html>
