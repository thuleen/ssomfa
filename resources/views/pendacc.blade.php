<!DOCTYPE html>
<html>

<head>
    <title>{{ config('app.name') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body {
            font-weight: 100;
            font-family: 'Oswald', sans-serif;
        }
    </style>
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
        <div class="mb-3">
            {{ $url }}
        </div>
    </div>
</body>

</html>