<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>File upload</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <h2 class="card-header w-100 m-1 text-center">Upload Image</h2>
        </div>
        @if($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        <div class="row justify-content-center">
            <form class="m-2" method="post" action="/upload-file" enctype="multipart/form-data">
                @csrf
                <div class="input-group mb-3">
                    <div class="custom-file">
                        <input type="file" name="file" class="custom-file-input" id="file">
                        <label class="custom-file-label" for="file">Choose file</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-dark d-block w-75 mx-auto">Upload</button>
            </form>
        </div>
        @if($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            <img class="row justify-content-center"
                 src="data:image/png;base64, {{ Session::get('encode_img') }}" style="width: 250px;height: 250px;">
            <form action="/download" method="post">
                @csrf
                <input type="hidden" name="file_download" value="{{ Session::get('encode_img') }}">
                <button type="submit" class="btn btn-secondary d-block w-75 mx-auto">Download watermark</button>
            </form>
        @endif
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
