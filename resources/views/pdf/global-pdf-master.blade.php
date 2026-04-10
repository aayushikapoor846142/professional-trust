

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        @page {
            margin-top: 0px; /* Top margin */
            margin-right: 5mm; /* Right margin */
            margin-bottom: 5mm; /* Bottom margin */
            margin-left: 5mm; /* Left margin */
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', sans-serif;
        }
        .invoice-box {
            max-width: 800px;
            margin: 2rem auto 0;
            padding: 20px;
            border: 1px solid #eee;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }
        .invoice-box table td {
            padding: 8px;
            vertical-align: top;
        }
        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
        .invoice-box table tr.top table td {
            /* padding-bottom: 20px; */
        }
        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.item.last td {
            border-bottom: none;
        }
        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .bill-to {
            text-align: left !important;
            border-left: 1px solid #ddd;
            padding: 13px !important;
        }
        .bill-from {
            text-align: left !important;
            padding: 13px !important;
        }
        .cds-invoice-logo { max-width: 170px;height: auto;background-color: #0a202b;padding: 7px;border-radius: 5px;}
    </style>
</head>
<body>
    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="https://trustvisory.com/assets/images/logo.png" class="cds-invoice-logo">
                                <!-- <img src="{{$logo_url}}" class="cds-invoice-logo"> -->
                            </td>
                            <td>
                               
                                <b>Invoice #</b>: {{$invoice_number}}<br>
                                <b>Created Date:</b> {{dateFormat($invoice['created_at'])}}<br>
                                @if($invoice['payment_status'] == 'paid')
                                <b> Paid Date:</b> {{dateFormat($invoice['paid_date'])}}<br>
                                @endif
                                
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td width="50%" class="bill-from">
                                <strong>Bill From:</strong><br>
                                {{$invoice['bill_from']}}
                            </td>
                            <td width="50%" class="bill-to">
                                <strong>Bill To:</strong><br>
                                {{$invoice['bill_to']}}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            @yield("content")
        </table>
    </div>
</body>
</html>
