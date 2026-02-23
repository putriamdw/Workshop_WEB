<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi OTP</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        .otp-input {
            font-size: 20px;
            text-align: center;
            letter-spacing: 10px;
            padding: 10px;
            width: 100%;
            margin-bottom: 15px;
        }

        button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #45a049;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Verifikasi OTP</h2>
    <p>Masukkan 6 digit kode OTP yang dikirim ke email Anda.</p>

    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('otp.verify') }}">
        @csrf
        <input type="text" 
               name="otp" 
               maxlength="6" 
               pattern="\d{6}" 
               class="otp-input"
               placeholder="______"
               required>

        <button type="submit">Verifikasi</button>
    </form>
</div>

</body>
</html>
