<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill ERMS PLUS AFRICA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #000;
            margin: 0;
            padding: 0;
            background: #f0f0f0;
        }

        .bill-container {
            width: 100%;
            max-width: 580px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            background: #fff;
            position: relative;
            overflow: hidden;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 100px;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.1);
            text-align: center;
            z-index: 0;
            white-space: nowrap;
        }

        .watermark img {
            display: block;
            margin: 10px auto 0;
            max-width: 200px;
            opacity: 0.1;
        }

        .bill-header,
        .bill-title,
        .bill-subtitle,
        .revenue-stream,
        .bill-details,
        .bill-no-details,
        .bill-table,
        .bill-table2,
        .legal,
        .signature,
        .note,
        .footer {
            position: relative;
            z-index: 1;
        }


        .bill-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px 0;
        }

        .header-left-logo {
            flex: 0 0 100px;
            text-align: center;
        }

        .header-center {
            flex: 1;
            text-align: center;
            padding: 0 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .header-right-logo {
            flex: 0 0 100px;
            text-align: center;
        }

        .header-center .assembly-name {
            font-size: 16px;
            font-weight: bold;
            margin: 3px 0;
            margin-top: -10px;
            text-transform: uppercase;
        }

        .header-center .assembly-address {
            font-size: 12px;
            margin: 3px 0 10px 0;
            margin-top: -3px;
            text-transform: uppercase;
        }

        .header-center .business-permit-box {
            border: 2px solid #020202;
            background-color: #ffffff;
            padding: 8px 16px;
            margin: 0;
            margin-top: -10px;
            border-radius: 5px;
            display: inline-block;
        }

        .header-center .business-permit-text {
            color: #010101;
            font-weight: bold;
            font-size: 14px;
            margin: 0;
        }

        .header-center .annual-bill-text {
            color: #020202;
            font-weight: bold;
            font-size: 16px;
            margin: 5px 0 10px 0;
        }

        .header-center .qr-code {
            margin-top: -2px;
        }

        .bill-title {
            text-align: center;
            font-weight: bold;
            font-size: 35px;
            margin: 0;
        }

        .bill-subtitle {
            text-align: center;
            font-size: 25px;
            margin: 5px 0 20px;
        }

        .revenue-stream {
            background: #f8f8f8;
            color: rgb(6, 6, 6);
            text-align: left;
            font-weight: bold;
            padding: 8px;
            margin-bottom: 20px;
            font-size: 18px;
        }

        .bill-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            margin-top: -10px;
            font-size: 12px;
        }

        .bill-no-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 12px;
        }

        .bill-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .bill-table2 {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 5px;
            font-size: 12px;
        }

        .bill-table th,
        .bill-table2 th {
            background: #000;
            color: #fff;
            text-align: left;
            padding: 6px;
        }

        .center_tag {
            text-align: center;
            padding: 6px;
        }

        .bill-table td,
        .bill-table2 td {
            border: 1px solid #ccc;
            padding: 6px;
        }

        .bill-table tbody tr:nth-child(even),
        .bill-table2 tbody tr:nth-child(even) {
            background: #f2f2f2;
        }

        .bill-table tr:last-child td {
            font-weight: bold;
            font-size: 14px;
            color: red;
        }

        .legal {
            text-align: left;
            margin: 10px 0;
            margin-top: -10px;
            font-size: 12px;
        }

        .signature {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            font-size: 12px;
        }

        .note {
            font-weight: bold;
            color: red;
            text-align: center;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #555;
            font-style: italic;
        }

        .download-btn {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            color: #fff;
            background: #000;
            border: none;
            cursor: pointer;
        }

        .download-btn:hover {
            background: #444;
        }

        @page {
            size: A5;
            margin: 10mm;
        }

        @media print {
            .download-btn {
                display: none;
            }

            body {
                background: #fff;
                margin: 0;
                font-size: 10px;
            }

            .bill-container {
                border: none;
                box-shadow: none;
                margin: 0;
                padding: 10px;
                width: auto;
                max-width: 100%;
            }

            .bill-header {
                margin-bottom: 8px;
                padding: 5px 0;
            }

            .header-left-logo img,
            .header-right-logo img {
                width: 50px !important;
                height: 50px !important;
            }

            .header-center .assembly-name {
                font-size: 12px;
                margin: 2px 0;
            }

            .header-center .assembly-address {
                font-size: 9px;
                margin: 2px 0 5px 0;
            }

            .header-center .business-permit-box {
                padding: 4px 10px;
                margin-top: -5px;
            }

            .header-center .business-permit-text {
                font-size: 10px;
            }

            .header-center .annual-bill-text {
                font-size: 11px;
                margin: 3px 0 5px 0;
            }

            .header-center .qr-code svg {
                width: 60px !important;
                height: 60px !important;
            }

            .revenue-stream {
                padding: 4px;
                margin-bottom: 8px;
                font-size: 12px;
            }

            .bill-details {
                margin-bottom: 3px;
                margin-top: -5px;
                font-size: 9px;
            }

            .bill-no-details {
                margin-bottom: 3px;
                font-size: 9px;
            }

            .bill-table,
            .bill-table2 {
                margin-bottom: 8px;
                font-size: 9px;
            }

            .bill-table th,
            .bill-table2 th {
                padding: 3px;
            }

            .bill-table td,
            .bill-table2 td {
                padding: 3px;
            }

            .bill-table tr:last-child td {
                font-size: 10px;
            }

            .legal {
                margin: 5px 0;
                margin-top: -5px;
                font-size: 8px;
                line-height: 1.3;
            }

            .signature {
                margin: 5px 0;
                font-size: 9px;
            }

            .note {
                margin-bottom: 5px;
                font-size: 9px;
            }

            .footer {
                font-size: 8px;
            }
        }
    </style>
</head>

<body>
    @php
        use SimpleSoftwareIO\QrCode\Facades\QrCode;
        $name = '';
        $account_number = '';
        $propertyUse = '';
        $zone = '';
        $assembly = '';
        $rateType = '';
        $phone = '';
        $rateableValue = 0;
        $basicRate = 0;
        if ($bill->property && $bill->property->customer) {
            $propertyId = $bill->property->erms_id;
            $billId = $bill->bills_id;
            $firstname = $bill->property->customer->fname ?? '';
            $lastname = $bill->property->customer->lname ?? '';
            $name = $firstname . ' ' . $lastname;
            $account_number = $bill->property->customer->erms_id ?? '';
            $location = $bill->property->location ?? '';
            $propertyUse = $bill->property->propertyUse->name ?? 'N/A';
            $zone = $bill->property->revenueStream->zoneType->name ?? 'N/A';
            $rateType = 'PROPERTY RATE';
            $phone = $bill->property->customer->phone_no_primary ?? 'N/A';
            $rateableValue = $bill->property->rateable_value ?? '';
            $revenueStream = $bill->property->revenueStream->revenueStreamType->name ?? '';
            $basicRate = number_format($bill->property->revenueStream->basic_rate, 2) ?? '0';
            $classType = $bill->property->revenueStream->propertyClasses->name ?? '';
        } elseif ($bill->business && $bill->business->customer) {
            $businessId = $bill->business->erms_id;
            $billId = $bill->bills_id;
            $firstname = $bill->business->customer->fname ?? '';
            $lastname = $bill->business->customer->lname ?? '';
            $name = $firstname . ' ' . $lastname;
            $account_number = $bill->business->customer->erms_id ?? '';
            $location = $bill->property->location ?? '';
            $propertyUse = $bill->business->propertyUse->name ?? 'N/A';
            $zone = $bill->business->revenueStream->itemDescription->name ?? 'N/A';
            $rateType = 'BUSINESS RATE';
            $phone = $bill->business->customer->phone_no_primary ?? 'N/A';
            $rateableValue = 0;
            $revenueStream = $bill->business->revenueStream->revenueStreamType->name ?? '';
            $basicRate = number_format($bill->business->revenueStream->basic_rate, 2) ?? '0';
        }
        if (str_contains($bill->assembly->name, 'Assembly')) {
            $assembly = $bill->assembly->name ?? 'N/A';
            $address = $bill->assembly->address ?? 'N/A';
        } else {
            $assembly = $bill->assembly->name . ' Assembly' ?? 'N/A';
            $address = $bill->assembly->address ?? 'N/A';
        }
        $billAmount = number_format($bill->amount, 2) ?? 0.0;
        $totalArrears = number_format($bill->arrears, 2) ?? 0.0;
        $discountAmount = number_format($bill->discount_amount, 2) ?? 0.0;
        $totalPayment = number_format($bill->total_payments_sum, 2) ?? 0.0;
        $totalAmount = $bill->amount + $bill->arrears - (float) ($bill->total_payments_sum ?? 0.0);
        $totalAmount = $totalAmount - (float) ($bill->discount_amount ?? 0.0);
        $totalAmountDue = number_format($totalAmount, 2) ?? 0.0;
        $qrContent = route('public.bills.show', $bill->bills_id);
        $qrCode = QrCode::size(90)->generate($qrContent);
    @endphp
    <div class="bill-container" id="bill">
        <div class="bill-header">
            <div class="header-left-logo">
                <img width="70" height="70" src="{{ asset('assets/images/arms.png') }}" alt="Ghana Coat of Arms" />
            </div>

            <div class="header-center">
                <div class="assembly-name">{{ strtoupper($assembly) }}</div>
                <div class="assembly-address">{{ strtoupper($address) }}</div>

                <div class="business-permit-box">
                    <div class="business-permit-text">
                        @if ($bill->property && $bill->property->customer)
                            PROPERTY RATE
                        @elseif ($bill->business && $bill->business->customer)
                            BUSINESS OPERATING PERMIT
                        @endif
                    </div>
                </div>

                <div class="annual-bill-text">ANNUAL BILL - YEAR {{ $bill->bills_year }}</div>

                <div class="qr-code">
                    {!! $qrCode !!}
                </div>
            </div>

            <div class="header-right-logo">
                @if ($bill->assembly->logo !== null)
                    <img width="70" height="70"
                        src="{{ asset('storage/images/logo/' . $bill->assembly->logo ?? '') }}" alt="Assembly Logo" />
                @endif
            </div>
        </div>

        <!-- Details -->
        <div class="bill-details">
            <div>
                <strong>Rate Payer Details</strong><br>
                Name of Rate Payer: <strong>{{ $name }}</strong><br>
                Rate Payer ID: <strong>{{ $account_number }}</strong><br>
                Location: <strong>{{ $location }}</strong>
            </div>
            <div>
                <strong>Revenue Item Details</strong><br>
                @if ($bill->property && $bill->property->customer)
                    Property ID: <strong>{{ $propertyId }}</strong><br>
                    Area Class: <strong>{{ $classType }}</strong><br>
                @else
                    Business ID: <strong>{{ $businessId }}</strong><br>
                    Business Descrip.: <strong>{{ $zone }}</strong><br>
                @endif
            </div>
        </div>
        <div class="bill-no-details">
            <div>
                Bill / Invoice No: <strong>{{ $billId }}</strong>
            </div>
        </div>
        <!-- Table -->
        <table class="bill-table">
            <thead>
                <tr>
                    <th>REVENUE ITEM DESCRIPTION</th>
                    <th>AMOUNT (GHS)</th>
                </tr>
            </thead>
            <tbody>
                @if ($bill->property && $bill->property->customer)
                    <tr>
                        <td>{{ $zone }}</td>
                        <td></td>
                    </tr>
                    @if ($bill->property && $bill->property->revenueStream->is_rated === 'Y')
                        <tr>
                            <td><strong>Rateable Value</strong></td>
                            <td class="center_tag">{{ number_format($bill->property->rateable_value, 2) ?? 0.0 }}</td>
                        </tr>
                        <tr>
                            <td><strong>Rate Impost</strong></td>
                            <td class="center_tag">
                                {{ number_format($bill->property->revenueStream->rate_impost, 2) ?? 0.0 }}
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Basic Rate</strong></td>
                            <td class="center_tag">{{ $basicRate }}</td>
                        </tr>
                    @else
                        <tr>
                            <td>{{ $bill->property->revenueStream->propertyType->name ?? 'N/A' }}</td>
                            <td></td>
                        </tr>
                    @endif
                @elseif ($bill->business && $bill->business->customer)
                    <tr>
                        <td>{{ $bill->business->revenueStream->category->name ?? 'N/A' }}</td>
                        <td></td>
                    </tr>
                @endif
                <tr>
                    <td><strong>Current Amount</strong></td>
                    <td class="center_tag">{{ $billAmount }}</td>
                </tr>
                <tr>
                    <td><strong>Arrears</strong></td>
                    <td class="center_tag">{{ $totalArrears }}</td>
                </tr>
                <tr>
                    <td><strong>Payment</strong></td>
                    <td class="center_tag">({{ $totalPayment }})</td>
                </tr>
                <tr>
                    <td><strong>Discount / Adjustment</strong></td>
                    <td class="center_tag">({{ $discountAmount }})</td>
                </tr>
                <tr>
                    <td><strong>Total Amount Due</strong></td>
                    <td class="center_tag">{{ $totalAmountDue }}</td>
                </tr>
            </tbody>
        </table>
        <!-- Legal + Bank Details -->
        <div class="legal">
            1. This bill is issued pursuant to section 137, 141 and 156 of the Local Governance Act, 2016 (Act 936) and
            the Bye-Laws of the Assembly. <br>
            2. This bill <u><strong>MUST BE PAID IN FULL</strong></u> on or before the Bill Due Date at the Assembly
            Office or at the bank. <br>
            3. Accredited Agents will instantly facilitate your payments through <strong>ELECTRONIC CHANNELS/CASHLESS
                TRANSACTIONS ONLY.</strong> <br>
            4. Refusal to pay your rates will make you liable to prosecution, pursuant to section 137, 141 and 156 of
            the Local Governance Act, 2016 (Act 936) and the Bye-Laws of the Assembly. <br>
            5. You can make self-payment by scanning the QR Code or dial USSD Code *800*12# and follow the instructions
            to make PAYMENT. <br>
            6. The Assembly's bank details are as follows
            <table class="bill-table2">
                <tr>
                    <td><strong>Bank Name</strong></td>
                    <td><strong>Bank Number</strong></td>
                    <td><strong>Bank Branch</strong></td>
                </tr>
                <tr>
                    <td>{{ $bill->assembly->bank_name ?? 'N/A' }}</td>
                    <td>{{ $bill->assembly->bank_number ?? 'N/A' }}</td>
                    <td>{{ $bill->assembly->bank_branch ?? 'N/A' }}</td>
                </tr>
            </table>
            <strong>Note:</strong> kindly quote the Bill No. and the account ID as reference. All dishonoured cheques
            will attract a penalty of 50%.
        </div>
        <!-- Signature Section -->
        <div class="signature">
            <div>Authorised Signature</div>
            <div>Contact: {{ $bill->assembly->phone ?? 'N/A' }}</div>
        </div>
    </div>
    <!-- Download Button -->
    <button class="download-btn" onclick="window.print()">Download Bill (A5 PDF)</button>
</body>

</html>
