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

        h1,
        h2,
        h5 {
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
        function copyToClipboard(id) {
            document.getElementById(id).select();
            document.execCommand('copy');
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const digitInputs = document.querySelectorAll('.otp-digit-inputs input');

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

    <script>
        function submitForm() {
            // Gather the values from digit inputs
            let digit1 = document.getElementById('otp_digit-1').value;
            let digit2 = document.getElementById('otp_digit-2').value;
            let digit3 = document.getElementById('otp_digit-3').value;
            let digit4 = document.getElementById('otp_digit-4').value;
            let digit5 = document.getElementById('otp_digit-5').value;

            // Check if all digits are entered
            if (digit1 && digit2 && digit3 && digit4 && digit5) {
                // If all digits are entered, submit the form
                document.getElementById('otpForm').submit();
            } else {
                // If any digit is missing, display an alert or handle it accordingly
                alert('Please enter all digits.');
            }
        }
    </script>
</head>

<body>
    <div class="d-flex flex-column justify-content-center align-items-center mt-3">
        <div class="d-flex flex-column flex-grow-1 justify-content-center align-items-center">
            <h2>1. Imbas QR</h2>
            <div>
                <img src={{ $dataUri }} alt='qr code' />
            </div>
            <div class="mt-3 mb-3">
                <h2>2. Masukkan OTP</h2>
            </div>
            <div class="d-flex flex-column mb-3 align-items-center">
                <form method="post" action="{{ route('ssomfa.submit.otp.form') }}" class="otp-digit-group" data-group-name="otp-digits" data-autosubmit="false" autocomplete="off" id="otpForm">
                    <div class="d-flex flex-column">
                        <div class="otp-digit-inputs mb-3">
                            <input type="text" id="digit-1" name="otp-digit-1" data-next="otp-digit-2" maxlength="1" />
                            <input type="text" id="digit-2" name="otp-digit-2" data-next="otp-digit-3" data-previous="otp-digit-1" maxlength="1" />
                            <input type="text" id="digit-3" name="otp-digit-3" data-next="otp-digit-4" data-previous="otp-digit-2" maxlength="1" />
                            <input type="text" id="digit-4" name="otp-digit-4" data-next="otp-digit-5" data-previous="otp-digit-3" maxlength="1" />
                            <input type="text" id="digit-5" name="otp-digit-5" data-next="otp-digit-6" data-previous="otp-digit-4" maxlength="1" />
                            <input hidden name="email" value='{{$email}}' />
                        </div>
                        <button type="submit" class="btn btn-primary thuleen-primary-btn">OK</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- developer console -->
        <div class="d-flex flex-row mt-5">
            <input class="form-control mb-1" type="text" value="{{ $url }}" id="urlField" readonly>
            <button value="copy" onclick="copyToClipboard('urlField')" class="btn btn-sm">Copy</button>
        </div>
    </div>

    @include('ssomfa::footer')
</body>

</html>