<!DOCTYPE html>
<html>
<head>
    <style>
        .button {
            background-color: #2563eb;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            display: inline-block;
            font-weight: bold;
        }
    </style>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <h2>Halo!</h2>
    <p>Kami menerima permintaan untuk mereset password akun LMS Anda.</p>
    <p>Silakan klik tombol di bawah ini untuk melanjutkan proses reset password:</p>
    
    <div style="margin: 30px 0;">
        <a href="{{ route('password.reset', $token) }}?email={{ request('email') }}" class="button">
            Reset Password Sekarang
        </a>
    </div>

    <p>Link ini akan kadaluarsa dalam 60 menit.</p>
    <p>Jika Anda tidak merasa meminta reset password, abaikan saja email ini.</p>
    <br>
    <p>Salam,<br><strong>NAHdev Team</strong></p>
</body>
</html>