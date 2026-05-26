<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #b30000; /* Merah Spekta */
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 2px;
        }
        .content {
            padding: 40px;
            text-align: center;
            color: #333333;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .otp-box {
            background-color: #fdf2f2;
            border: 2px dashed #b30000;
            color: #b30000;
            font-size: 36px;
            font-weight: bold;
            padding: 20px;
            letter-spacing: 10px;
            display: inline-block;
            margin: 10px 0;
            border-radius: 8px;
        }
        .footer {
            background-color: #f9f9f9;
            color: #777777;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            border-top: 1px solid #eeeeee;
        }
        .warning {
            color: #888888;
            font-size: 13px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>SPEKTA ACADEMY</h1>
        </div>

        <!-- Konten Utama -->
        <div class="content">
            <p>Halo,</p>
            <p>Terima kasih telah mendaftar di <strong>Spekta Academy</strong>. Untuk menyelesaikan proses verifikasi akun Anda, silakan gunakan kode OTP berikut:</p>
            
            <div class="otp-box">
                {{ $otp }}
            </div>

            <p class="warning">
                Kode ini berlaku selama <strong>5 menit</strong>.<br>
                Demi keamanan akun Anda, jangan berikan kode ini kepada siapa pun.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} Spekta Academy. Semua Hak Dilindungi.<br>
            Jl. Pendidikan No. 123, Indonesia.</p>
        </div>
    </div>
</body>
</html>