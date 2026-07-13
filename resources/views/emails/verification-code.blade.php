<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kode Verifikasi Akun</title>
    <style>
        body {
            font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #FCFAF7;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .container {
            max-width: 500px;
            margin: 40px auto;
            background-color: #ffffff;
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(139, 69, 19, 0.03);
        }
        .header {
            background-color: #8B4513;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .content {
            padding: 40px 30px;
            line-height: 1.6;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #4a3e3d;
        }
        .message {
            font-size: 14px;
            color: #666666;
            margin-bottom: 30px;
        }
        .code-box {
            background-color: #fcfcfc;
            border: 1px dashed #8B4513;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 6px;
            color: #8B4513;
        }
        .expiry {
            font-size: 12px;
            color: #999999;
            text-align: center;
            margin-top: -20px;
            margin-bottom: 30px;
        }
        .footer {
            background-color: #fafafa;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #999999;
            border-top: 1px solid #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>BATIK GIRI ALAM</h1>
        </div>
        <div class="content">
            <div class="greeting">Halo {{ $userName }},</div>
            <div class="message">
                Terima kasih telah mendaftar di Batik Giri Alam. Silakan gunakan kode verifikasi di bawah ini untuk mengaktifkan akun Anda:
            </div>
            <div class="code-box">
                <span class="code">{{ $code }}</span>
            </div>
            <div class="expiry">
                *Kode verifikasi ini berlaku selama 15 menit.
            </div>
            <div class="message" style="margin-bottom: 0;">
                Jika Anda tidak merasa melakukan pendaftaran di website kami, silakan abaikan email ini.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Batik Giri Alam. All rights reserved.
        </div>
    </div>
</body>
</html>
