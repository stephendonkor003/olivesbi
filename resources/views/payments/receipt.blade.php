<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <style>
        :root {
            --primary-rgb: 0, 191, 255;
            --primary: rgb(var(--primary-rgb));
        }

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .receipt-container {
            max-width: 650px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 26px;
            color: var(--primary);
            margin: 0;
        }

        .header p {
            font-size: 14px;
            color: #555;
            margin: 5px 0;
        }

        .receipt-details {
            font-size: 14px;
            margin-bottom: 20px;
        }

        .receipt-details .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .summary {
            background-color: #f1f8fa;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #d5ebf3;
            margin-bottom: 25px;
        }

        .summary h3 {
            font-size: 18px;
            color: var(--primary);
            margin-bottom: 10px;
            text-align: center;
        }

        .summary p {
            font-size: 16px;
            text-align: center;
            color: #333;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
        }

        .print-btn-container {
            text-align: center;
            margin-top: 20px;
        }

        .btn-print {
            background-color: var(--primary);
            border: none;
            color: #fff;
            padding: 10px 25px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-print:hover {
            background-color: rgba(var(--primary-rgb), 0.9);
        }

        @media print {

            .btn-print,
            .print-btn-container {
                display: none;
            }

            body {
                background-color: #ffffff;
                padding: 0;
            }

            .receipt-container {
                box-shadow: none;
                border: none;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    @php
        $billType = '';
        $name = '';
        $totalDue = 0;

        if ($payment->bill->property_id !== null) {
            $firstname = $payment->bill->property->customer->fname ?? '';
            $lastname = $payment->bill->property->customer->lname ?? '';
            $billType = 'Property Bill';
        } else {
            $firstname = $payment->bill->business->customer->fname ?? '';
            $lastname = $payment->bill->business->customer->lname ?? '';
            $billType = 'Business Bill';
        }

        $name = $firstname . ' ' . $lastname;
        $totalDue = $payment->bill->arrears + $payment->bill->amount;
    @endphp

    <div class="receipt-container" id="receipt">
        <!-- Header -->
        <div class="header">
            <h1>Payment Receipt</h1>
            <p>{{ $payment->bill->assembly->name ?? 'District Assembly' }}</p>
            <p><strong>Address:</strong> {{ $payment->bill->assembly->address ?? 'N/A' }} |
                <strong>Phone:</strong> {{ $payment->bill->assembly->phone ?? 'N/A' }}
            </p>
        </div>

        <!-- Receipt Details -->
        <div class="receipt-details">
            <div class="row">
                <span><strong>Receipt No:</strong> {{ $payment->bill->bills_id }}</span>
                <span><strong>Date:</strong> {{ now()->format('d M, Y') }}</span>
            </div>
            <div class="row">
                <span><strong>Customer Name:</strong> {{ $name }}</span>
                <span><strong>Payment Mode:</strong> {{ ucfirst($payment->payment_mode) }}</span>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="summary">
            <h3>Payment Summary</h3>
            <p><strong>Amount Paid:</strong> GHS {{ number_format($payment->amount, 2) }}</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your payment!</p>
            <p>This receipt is system generated and does not require a signature.</p>
        </div>
    </div>

    <!-- Print Button -->
    <div class="print-btn-container">
        <button class="btn-print" onclick="window.print()">Download PDF</button>
    </div>
</body>

</html>
