<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agreement - {{ $agreement->template_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
        }
        .header .subtitle {
            color: #7f8c8d;
            margin: 5px 0;
            font-size: 14px;
        }
        .agreement-info {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .agreement-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .agreement-info td {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        .agreement-info td:first-child {
            font-weight: bold;
            width: 30%;
        }
        .agreement-content {
            margin-bottom: 30px;
            text-align: justify;
        }
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin-top: 30px;
            display: inline-block;
        }
        .signature-box {
            display: inline-block;
            margin-right: 50px;
            text-align: center;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #7f8c8d;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }
        .page-number {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Professional Associate Agreement</h1>
        <div class="subtitle">Agreement ID: {{ $agreement_id }}</div>
        <div class="subtitle">Generated on: {{ $generated_date }}</div>
    </div>

    <div class="agreement-info">
        <table>
            <tr>
                <td>Agreement Name:</td>
                <td>{{ $agreement->template_name }}</td>
            </tr>
            <tr>
                <td>Professional:</td>
                <td>{{ $professional->first_name }} {{ $professional->last_name }}</td>
            </tr>
            <tr>
                <td>Associate:</td>
                <td>{{ $associate->first_name }} {{ $associate->last_name }}</td>
            </tr>
            <tr>
                <td>Platform Fees:</td>
                <td>${{ number_format($agreement->platform_fees, 2) }}</td>
            </tr>
            <tr>
                <td>Sharing Fees:</td>
                <td>{{ $agreement->sharing_fees }}%</td>
            </tr>
            <tr>
                <td>Agreement Date:</td>
                <td>{{ $generated_date }}</td>
            </tr>
        </table>
    </div>

    <div class="agreement-content">
        {!! $agreement->agreement !!}
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <p><strong>{{ $professional->first_name }} {{ $professional->last_name }}</strong></p>
            <p>Professional</p>
            <p>Date: {{ $generated_date }}</p>
        </div>

        <div class="signature-box">
            <div class="signature-line"></div>
            <p><strong>{{ $associate->first_name }} {{ $associate->last_name }}</strong></p>
            <p>Associate</p>
            <p>Date: ________________</p>
        </div>
    </div>

    <div class="footer">
        <p>This agreement is generated electronically and is legally binding when signed by both parties.</p>
        <p>For any questions regarding this agreement, please contact the platform administrator.</p>
    </div>

    <div class="page-number">
        Page 1 of 1
    </div>
</body>
</html>
