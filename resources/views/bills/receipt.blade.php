<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="" xml:lang="">

<head>
    <title>Bill Receipt</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <br />
    <style type="text/css">
        p {
            margin: 0;
            padding: 0;
        }

        .ft10 {
            font-size: 11px;
            font-family: Times;
            color: #000000;
        }

        .ft11 {
            font-size: 14px;
            font-family: Times;
            color: #000000;
        }

        .ft12 {
            font-size: 17px;
            font-family: Times;
            color: #000000;
        }

        .center-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .page-content {
            position: relative;
            width: 892px;
            height: 630px;
        }

        .note {
            font-size: 12px;
            margin-bottom: 20px;
            text-align: justify;
        }
    </style>
</head>

<body bgcolor="#A0A0A0" vlink="blue" link="blue">
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
            $firstname = $bill->property->customer->fname ?? '';
            $lastname = $bill->property->customer->lname ?? '';
            $name = $firstname . ' ' . $lastname;
            $account_number = $bill->property->erms_id ?? '';
            $propertyUse = $bill->property->propertyUse->name ?? 'N/A';
            $zone = $bill->property->zone->name ?? 'N/A';
            $rateType = 'PROPERTY RATE';
            $phone = $bill->property->customer->phone_no_primary ?? 'N/A';
            $rateableValue = $bill->property->rateable_value ?? '';
        } elseif ($bill->business && $bill->business->customer) {
            $firstname = $bill->business->customer->fname ?? '';
            $lastname = $bill->business->customer->lname ?? '';
            $name = $firstname . ' ' . $lastname;
            $account_number = $bill->business->erms_id ?? '';
            $propertyUse = $bill->business->propertyUse->name ?? 'N/A';
            $zone = $bill->business->zone->name ?? 'N/A';
            $rateType = 'BUSINESS RATE';
            $phone = $bill->business->customer->phone_no_primary ?? 'N/A';
            $rateableValue = 0;
        }

        if (str_contains($bill->assembly->name, 'Assembly')) {
            $assembly = $bill->assembly->name ?? 'N/A';
        } else {
            $assembly = $bill->assembly->name . ' Assembly' ?? 'N/A';
        }

        $totalAmount = $bill->arrears + $bill->amount + $basicRate;
        $qrContent = route('bills.show', $bill);
        $qrCode = QrCode::size(100)->generate($qrContent);
    @endphp

    <div class="center-wrapper">
        <div id="page1-div" class="page-content">
            <img width="892" height="630" src="{{ asset('assets/images/template/bill_receipt.jpeg') }}"
                alt="background image" />
            <p style="position:absolute;top:20px;left:365px;white-space:nowrap;font-size:18px;text-align:center"
                class="ft11">
                <b>{{ strtoupper($assembly) }}</b>
            </p>
            <div style="position:absolute;top:8px;left:820px;white-space:nowrap;">
                @if ($bill->assembly->logo !== null)
                    <img width="70" height="75"
                        src="{{ asset('storage/images/logo/' . $bill->assembly->logo ?? '') }}" alt="assembly image" />
                @endif
            </div>
            {{-- <p style="position:absolute;top:60px;left:470px;white-space:nowrap;font-size:18px;color:blue"
                class="ft11"><b>{{ strtoupper($rateType) }}</b>
            </p> --}}
            <p style="position:absolute;top:20px;left:40px;white-space:nowrap;font-size:12px" class="ft11">
                <b>{{ strtoupper($assembly) }}</b>
            </p>
            <p style="position:absolute;top:92px;left:120px;white-space:nowrap" class="ft11">
                <b>{{ $account_number }}</b>
            </p>
            <p style="position:absolute;top:172px;left:77px;white-space:nowrap" class="ft11">
                <b>SOUTH&#160;ODORKOR</b>
            </p>
            <p style="position:absolute;top:208px;left:60px;white-space:nowrap" class="ft11">
                <b>{{ strtoupper($propertyUse) }}</b>
            </p>
            <p style="position:absolute;top:258px;left:96px;white-space:nowrap" class="ft11"><b>FIRST&#160;CLASS</b>
            </p>
            <p style="position:absolute;top:275px;left:92px;white-space:nowrap" class="ft11">
                <b>{{ strtoupper($zone) }}</b>
            </p>
            <p style="position:absolute;top:335px;left:150px;white-space:nowrap" class="ft10">
                <b>{{ number_format($rateableValue, 2) }}</b>
            </p>
            <p style="position:absolute;top:364px;left:150px;white-space:nowrap" class="ft10">
                <b>{{ $bill->rate_imposed }}</b>
            </p>
            <p style="position:absolute;top:430px;left:140px;white-space:nowrap" class="ft10">
                <b>{{ number_format($bill->arrears, 2) }}</b>
            </p>
            <p style="position:absolute;top:462px;left:140px;white-space:nowrap" class="ft10">
                <b>{{ number_format($bill->amount, 2) }}</b>
            </p>
            {{-- <p style="position:absolute;top:462px;left:140px;white-space:nowrap" class="ft10">
                <b>{{ number_format($basicRate, 2) }}</b>
            </p> --}}
            <p style="position:absolute;top:500px;left:140px;white-space:nowrap" class="ft10">
                <b>{{ number_format($totalAmount, 2) }}</b>
            </p>
            <p style="position:absolute;top:172px;left:377px;white-space:nowrap" class="ft12">
                <b>SOUTH&#160;ODORKOR</b>
            </p>
            <p style="position:absolute;top:204px;left:377px;white-space:nowrap" class="ft12">
                <b>{{ strtoupper($propertyUse) }}</b>
            </p>
            <p style="position:absolute;top:238px;left:377px;white-space:nowrap" class="ft12">
                <b>FIRST&#160;CLASS&#160;{{ strtoupper($zone) }}</b>
            </p>
            <p style="position:absolute;top:320px;left:480px;white-space:nowrap" class="ft11">
                <b>{{ number_format($rateableValue, 2) }}</b>
            </p>
            <p style="position:absolute;top:368px;left:480px;white-space:nowrap" class="ft11">
                <b>{{ $bill->rate_imposed }}</b>
            </p>
            <p style="position:absolute;top:295px;left:770px;white-space:nowrap" class="ft11">
                <b>{{ number_format($bill->arrears, 2) }}</b>
            </p>
            <p style="position:absolute;top:325px;left:770px;white-space:nowrap" class="ft11">
                <b>{{ number_format($bill->amount, 2) }}</b>
            </p>
            {{-- <p style="position:absolute;top:345px;left:730px;white-space:nowrap" class="ft11">
                <b>{{ number_format($basicRate, 2) }}</b>
            </p> --}}
            <p style="position:absolute;top:360px;left:770px;white-space:nowrap" class="ft11">
                <b>{{ number_format($totalAmount, 2) }}</b>
            </p>
            <p style="position:absolute;top:95px;left:373px;white-space:nowrap" class="ft12">
                <b>{{ $account_number }}</b>
            </p>
            <p style="position:absolute;top:133px;left:375px;white-space:nowrap" class="ft12">
                <b>{{ $name }}</b>
            </p>
            <p style="position:absolute;top:97px;left:780px;white-space:nowrap" class="ft12">
                <b>{{ $bill->bills_year }}</b>
            </p>
            <p style="position:absolute;top:255px;left:717px;white-space:nowrap" class="ft12">
                <b>Tel: {{ $phone }}</b>
            </p>
            <p style="position:absolute;top:423px;text-align:left;font-size:15px;line-height:1.0;margin:0;left:284px;width:calc(100% - 350px);"
                class="ft11">
                This bill <b>MUST BE PAID IN FULL BEFORE 1st April {{ date('Y') }}</b> or within two weeks of the
                delivery date at
                the {{ $assembly }}, Darkuman Kokompe, or to the {{ $assembly }} GCB Account
                Number <b>1501130001178</b> Lapaz Branch referencing your Bill Account Number or to any Accredited
                Agent. All dishonored cheques will attract a penalty of 50% and the issuer will be liable to
                prosecution, pursuant to section 137, 141, and 156 of the Local Governance Act, 2016 (Act 936) and the
                Bye-Laws of the {{ $assembly }}.
            </p>
            {{-- <p style="position:absolute;top:530px;left:280px;white-space:nowrap">
                For inquries or payment, please call:<br><b>{{ $bill->assembly->phone ?? 'N/A' }}</b>
            </p> --}}
            <div style="position:absolute;top:130px;left:785px;">
                {!! $qrCode !!}
            </div>
        </div>
    </div>
</body>

</html>
