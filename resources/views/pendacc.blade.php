<!DOCTYPE html>
<html>

<head>
    <title>{{ config('app.name') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/8627768fb0.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-weight: 100;
            font-family: 'Oswald', sans-serif;
        }

        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            text-align: center;
            padding: 10px 0;
            background-color: #f8f9fa;
            font-size: small;
            color: #273c75;
        }

        h1 {
            color: #273c75;
        }
    </style>

    <script>
        function copyToClipboard(id) {
            document.getElementById(id).select();
            document.execCommand('copy');
        }
    </script>
</head>

<body>
    <div class="d-flex flex-column justify-content-center align-items-center m-3">
        <h1>Imbas</h1>
        <div class="mb-3">
            Imbas guna kamera phone
        </div>
        <div class="mb-3">
            <img src={{ $dataUri }} alt='qr code' />
        </div>
        <div class="d-flex flex-row mb-3">
            <input class="form-control mr-1" type="text" value="{{ $url }}" id="urlField" readonly>
            <button value="copy" onclick="copyToClipboard('urlField')" class="btn btn-outline-primary">Copy</button>
        </div>
    </div>
    @include('ssomfa::footer')
</body>

</html>