<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Agreement - {{ $agreement->unique_id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(45deg, transparent 40%, rgba(0,123,255,0.02) 40%, rgba(0,123,255,0.02) 60%, transparent 60%),
                linear-gradient(-45deg, transparent 40%, rgba(0,123,255,0.02) 40%, rgba(0,123,255,0.02) 60%, transparent 60%);
            background-size: 20px 20px;
            pointer-events: none;
            z-index: -1;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #2c3e50;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        
        .header .subtitle {
            color: #7f8c8d;
            font-size: 14px;
            margin: 0;
        }
        
        .agreement-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 25px;
        }
        
        .agreement-info h3 {
            color: #2c3e50;
            margin: 0 0 15px 0;
            font-size: 16px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 8px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 30%;
            padding: 8px 0;
            color: #495057;
        }
        
        .info-value {
            display: table-cell;
            padding: 8px 0;
            color: #333;
        }
        
        .agreement-content {
            margin-bottom: 25px;
        }
        
        .agreement-content h3 {
            color: #2c3e50;
            margin: 0 0 15px 0;
            font-size: 16px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 8px;
        }
        
        .content-body {
            text-align: justify;
            line-height: 1.8;
        }
        
        .content-body h1, .content-body h2, .content-body h3, 
        .content-body h4, .content-body h5, .content-body h6 {
            color: #2c3e50;
            margin: 20px 0 10px 0;
        }
        
        .content-body p {
            margin-bottom: 15px;
        }
        
        .content-body table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .content-body table td, .content-body table th {
            border: 1px solid #dee2e6;
            padding: 8px;
        }
        
        .content-body table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #6c757d;
            font-size: 10px;
        }
        
        .signature-section {
            margin-top: 30px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 45%;
            vertical-align: top;
            padding: 15px;
        }
        
        .signature-box:first-child {
            padding-right: 30px;
        }
        
        .signature-box:last-child {
            padding-left: 30px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 10px;
            text-align: center;
        }
        
        .signature-label {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .signature-name {
            color: #333;
            font-size: 12px;
        }
        
        .signature-date {
            color: #6c757d;
            font-size: 10px;
            margin-top: 5px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Professional Agreement</h1>
        <p class="subtitle">Agreement ID: {{ $agreement->unique_id }}</p>
        <p class="subtitle">Generated on: {{ $generated_at }}</p>
    </div>

    <!-- Agreement Information -->
    <div class="agreement-info">
        <h3>Agreement Details</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Agreement Name:</div>
                <div class="info-value">{{ $agreement->template_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Agreement ID:</div>
                <div class="info-value">{{ $agreement->unique_id }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Professional:</div>
                <div class="info-value">
                    {{ $professional ? $professional->first_name . ' ' . $professional->last_name : 'N/A' }}
                    @if($professional)
                        <br><small>(ID: {{ $professional->id }})</small>
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Associate:</div>
                <div class="info-value">
                    {{ $associate ? $associate->first_name . ' ' . $associate->last_name : 'N/A' }}
                    @if($associate)
                        <br><small>(ID: {{ $associate->id }})</small>
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Platform Fees:</div>
                <div class="info-value">{{ $agreement->platform_fees }}%</div>
            </div>
            <div class="info-row">
                <div class="info-label">Sharing Fees:</div>
                <div class="info-value">{{ $agreement->sharing_fees }}%</div>
            </div>
            <div class="info-row">
                <div class="info-label">Created Date:</div>
                <div class="info-value">
                    {{ $agreement->created_at ? $agreement->created_at->format('F d, Y') : 'N/A' }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    @if($agreement->is_support_accept == 1)
                        <span style="color: #28a745; font-weight: bold;">✓ Approved</span>
                    @else
                        <span style="color: #ffc107; font-weight: bold;">⏳ Under Review</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Agreement Content -->
    <div class="agreement-content">
        <h3>Agreement Terms & Conditions</h3>
        <div class="content-body">
            {!! $agreement->agreement !!}
        </div>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                <div class="signature-label">Professional Signature</div>
                <div class="signature-name">
                    {{ $professional ? $professional->first_name . ' ' . $professional->last_name : 'N/A' }}
                </div>
                <div class="signature-date">Date: {{ date('F d, Y') }}</div>
            </div>
        </div>
        
        <div class="signature-box">
            <div class="signature-line">
                <div class="signature-label">Associate Signature</div>
                <div class="signature-name">
                    {{ $associate ? $associate->first_name . ' ' . $associate->last_name : 'N/A' }}
                </div>
                <div class="signature-date">Date: {{ date('F d, Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This document was generated electronically on {{ $generated_at }}</p>
        <p>Agreement ID: {{ $agreement->unique_id }} | Page 1 of 1</p>
    </div>
</body>
</html>
