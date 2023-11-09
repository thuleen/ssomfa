<!DOCTYPE html>
<html>

<head>
    <title>{{ config('app.name') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

        h1,
        h5 {
            color: #273c75;
        }

        .digit-group {
            display: flex;
        }

        .digit-group input {
            width: 3rem;
            /* Adjust the width as needed */
            height: 3rem;
            /* Make the input fields square */
            text-align: center;
            margin: 0 0.2rem;
            /* Add some spacing between input fields */
        }
    </style>

    <script>
        function copyToClipboard(id) {
            document.getElementById(id).select();
            document.execCommand('copy');
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const digitInputs = document.querySelectorAll('.digit-inputs input');

            digitInputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    let value = this.value;
                    // Remove any non-numeric characters
                    value = value.replace(/\D/g, '');
                    this.value = value;

                    if (value.length >= 1) {
                        if (index < digitInputs.length - 1) {
                            digitInputs[index + 1].focus();
                        }
                    }
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && this.value === '') {
                        if (index > 0) {
                            digitInputs[index - 1].focus();
                        }
                    } else if (e.key === 'Delete' && this.value === '' && index > 0) {
                        digitInputs[index - 1].focus();
                    }
                });
            });
        });
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
        <div class="mb-3">
            <h5>dan masukkan 5 digit nombor:</h5>
        </div>
        <div class="d-flex flex-column mb-3 align-items-center">
            <form method="get" class="digit-group" data-group-name="digits" data-autosubmit="false" autocomplete="off">
                <div class="d-flex flex-column">
                    <div class="digit-inputs mb-3">
                        <input type="text" id="digit-1" name="digit-1" data-next="digit-2" maxlength="1" />
                        <input type="text" id="digit-2" name="digit-2" data-next="digit-3" data-previous="digit-1" maxlength="1" />
                        <input type="text" id="digit-3" name="digit-3" data-next="digit-4" data-previous="digit-2" maxlength="1" />
                        <input type="text" id="digit-4" name="digit-4" data-next="digit-5" data-previous="digit-3" maxlength="1" />
                        <input type="text" id="digit-5" name="digit-5" data-next="digit-6" data-previous="digit-4" maxlength="1" />
                    </div>
                    <button type="submit" class="btn btn-primary">OK</button>
                </div>
            </form>
        </div>
    </div>
    <div class="d-flex flex-row mb-3 align-items-center ">
        <input class="form-control mr-1" type="text" value="{{ $url }}" id="urlField" readonly>
        <button value="copy" onclick="copyToClipboard('urlField')" class="btn btn-outline-primary">Copy</button>
    </div>
    @include('ssomfa::footer')
</body>

</html>