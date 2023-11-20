<!DOCTYPE html>
<html>

<head>
    <title>{{ config('app.name') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200;500&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;500&family=Oswald:wght@200;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/8627768fb0.js" crossorigin="anonymous"></script>
    <style>
        .btn-thuleen {
            font-family: 'Oswald', sans-serif;
            background-color: #273c75;
            border: none;
        }

        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            text-align: center;
            padding: 10px 0;
            background-color: #f8f9fa;
            font-family: 'Open Sans', sans-serif;
            font-weight: 500;
            font-size: small;
            color: #273c75;
        }

        .code-blue {
            color: #273c75;
            /* or any other shade of blue you prefer */
            font-size: small;
        }

        .warning-text {
            font-family: 'Open Sans', sans-serif;
            color: #c23616;
        }

        h1,
        h2,
        h3,
        h5 {
            font-family: 'Oswald', sans-serif;
            color: #273c75;
        }

        .otp-digit-group {
            display: flex;
        }

        .otp-digit-group input {
            width: 3rem;
            /* Adjust the width as needed */
            height: 3rem;
            /* Make the input fields square */
            text-align: center;
            margin: 0 0.2rem;
            /* Add some spacing between input fields */
        }

        .thuleen-primary-btn {
            border: none;
            background-color: #192a56;
            color: #fff;
        }
    </style>
    <script>
        function reloadWindow() {
            location.reload();
        }
    </script>
</head>

<body>
    <div class="d-flex flex-column justify-content-center align-items-center mt-5">
        <div class="alert alert-warning" role="alert">
            <h3>thuleen</h3>
            Please ensure smart contract is identical to DApp at {{ $dappUrl }}
            <br />
            Once resolve, refresh this page.
            <br />
            <button class="btn btn-primary mt-3 btn-thuleen" type="submit" onclick="reloadWindow()">REFRESH</button>
        </div>
    </div>

    @include('ssomfa::footer')
</body>

</html>