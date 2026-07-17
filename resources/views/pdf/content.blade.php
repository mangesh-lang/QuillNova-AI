<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #2D3748;
            line-height: 1.6;
            margin: 40px;
        }
        h1 {
            font-size: 24px;
            color: #1A202C;
            border-bottom: 2px solid #E2E8F0;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .meta {
            font-size: 12px;
            color: #718096;
            margin-bottom: 30px;
        }
        .content {
            font-size: 14px;
            white-space: pre-wrap;
        }
        .footer {
            margin-top: 50px;
            font-size: 10px;
            color: #A0AEC0;
            text-align: center;
            border-top: 1px solid #E2E8F0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="meta">Generated via QuillNova AI — {{ now()->format('F d, Y h:i A') }}</div>
    <div class="content">{!! nl2br(e($content)) !!}</div>
    <div class="footer">
        QuillNova SaaS Platform &copy; {{ date('Y') }}. All rights reserved.
    </div>
</body>
</html>
