<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #000;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            margin-top: 20px;
        }
        .field {
            margin-bottom: 15px;
        }
        .field-label {
            font-weight: bold;
            color: #555;
        }
        .field-value {
            margin-top: 5px;
            padding: 10px;
            background-color: #fff;
            border-left: 3px solid #000;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Contact Form Submission</h1>
        <p>Batik Giri Alam Gumelem Wetan</p>
    </div>

    <div class="content">
        <p>You have received a new message from your website contact form.</p>

        <div class="field">
            <div class="field-label">Name:</div>
            <div class="field-value">{{ $data['name'] }}</div>
        </div>

        <div class="field">
            <div class="field-label">Email:</div>
            <div class="field-value">
                <a href="mailto:{{ $data['email'] }}">{{ $data['email'] }}</a>
            </div>
        </div>

        <div class="field">
            <div class="field-label">Subject:</div>
            <div class="field-value">{{ $data['subject'] }}</div>
        </div>

        @if(!empty($data['message']))
        <div class="field">
            <div class="field-label">Message:</div>
            <div class="field-value">{{ $data['message'] }}</div>
        </div>
        @endif

        <div class="field">
            <div class="field-label">Submitted at:</div>
            <div class="field-value">{{ now()->format('d F Y, H:i:s') }}</div>
        </div>
    </div>

    <div class="footer">
        <p>This email was sent from Batik Giri Alam Gumelem Wetan contact form</p>
        <p>You can reply directly to this email to respond to {{ $data['name'] }}</p>
    </div>
</body>
</html>