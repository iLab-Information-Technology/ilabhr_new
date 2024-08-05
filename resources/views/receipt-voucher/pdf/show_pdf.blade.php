<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Voucher</title>

    <style>
        @font-face {
            font-family: 'NotoKufiArabic';
            src: url('{{asset("fonts/NotoKufiArabic-Regular.ttf")}}') format('truetype');
        }

        body {
            font-family: 'NotoKufiArabic', serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
            font-weight: normal;
        }

        .invoice-container {
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
            display: grid;
            grid-template-rows: auto auto auto auto auto auto;
            gap: 0;
        }

        .header,
        .details,
        .items-5,
        .items-4,
        .items-3 {
            display: grid;
            gap: 0;
            border-collapse: collapse;
        }

        .header {
            grid-template-columns: 1fr 1fr;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .header img {
            max-width: 150px;
        }

        .header div {
            text-align: right;
            font-weight: bold;
            font-size: 21px;
            text-transform: uppercase;
            color: #000;
        }

        .details {
            grid-template-columns: 1fr 1fr;
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }

        .details>div {
            display: flex;
            flex-direction: column;
        }

        .details p {
            margin: 0;
        }

        .details .inv-num-date {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0px;
        }

        .details .inv-num-date div {
            padding: 5px;
            border: 1px solid #ddd;
            text-align: right;
        }

        .details .inv-num-date div:first-child {

            font-weight: bold;
        }

        .cell-heading {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .items-5 {
            grid-template-columns: repeat(5, 1fr);
            border-bottom: 1px solid #ddd;
        }

        .items-4 {
            grid-template-columns: repeat(4, 1fr);
            border-bottom: 1px solid #ddd;
        }

        .items-3 {
            grid-template-columns: repeat(3, 1fr);
            border-bottom: 1px solid #ddd;
        }

        .items-5>div,
        .items-4>div,
        .items-3>div {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .headings {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .signatures {
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: auto auto;
            border-bottom: 1px solid #ddd;
        }

        .signatures>div {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .signature-line {
            padding: 50px 10px 2px 10px;
            font-family: "Playwrite CU", cursive;
            font-optical-sizing: auto;
            font-weight: 600;
            font-style: normal;
            font-size: 30px;
        }

        @media print {
            .invoice-container {
                padding: 0;
            }

            .header,
            .details,
            .items-5,
            .items-4,
            .items-3,
            .signatures {
                gap: 0;
            }

            .header div {
                font-size: 18px;
            }

            .items-5>div,
            .items-4>div,
            .items-3>div,
            .signatures>div {
                padding: 4px;
            }

            .signature-line {
                padding: 30px 5px 1px 5px;
                font-family: "Playwrite CU", cursive;
                font-optical-sizing: auto;
                font-weight: 600;
                font-style: normal;
                font-size: 30px;
            }
        }

        .rtl {
            direction: rtl;
        }

        /* PDF-specific styles */
        .pdf-style {
            font-size: 28px;
            /* Larger font size for PDF */
        }

        /* Loader styles */
        .loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 1);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loader {
            width: 50px;
            height: 50px;
            border: 10px solid #f3f3f3;
            border-radius: 50%;
            border-top: 10px solid #3498db;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Styles for PDF content */
        .invoice-table-wrapper {
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }
    </style>
</head>

<body>

    <div class="loader-wrapper" id="loader-wrapper">
        <div class="loader" id="loader"></div>
    </div>
    <div class="invoice-container" id="content">
        <div class="header">
            <img src="{{ asset('user-uploads/app-logo/06eace9938c8f15983f12a5430e8b294.png') }}" alt="ilab Information Technologies" id="logo">
            <div>سند القبض</div>
        </div>

        <div class="details">
            <div>
                <p>
                    ilab Information Technologies<br>
                    +966920008946
                </p>
            </div>
            <div class="inv-num-date">
                <div>{{ $receiptVoucher->voucher_number }}</div>
                <div class="cell-heading"> <b>عدد إيصال</b></div>
                <div>{{ $receiptVoucher->voucher_date->translatedFormat(company()->date_format) }}</div>

                <div class="cell-heading">تاريخ استلام</div>
            </div>
        </div>

        <div class="details">
            <div>
                <p class="mb-0 text-left">
                    @if (
                    ($receiptVoucher->driver) &&
                    ($receiptVoucher->driver->name ||
                    $receiptVoucher->driver->email ||
                    $receiptVoucher->driver->work_mobile_no))
                    <span class="text-dark-grey text-capitalize">تلقى من السائق</span><br>

                    @if ($receiptVoucher->driver && $receiptVoucher->driver->name)
                    {{ $receiptVoucher->driver->name }}<br>
                    @endif

                    @if ($receiptVoucher->driver && $receiptVoucher->driver->email)
                    {{ $receiptVoucher->driver->email }}<br>
                    @endif

                    @if ($receiptVoucher->driver && $receiptVoucher->driver->work_mobile_with_phone_code)
                    {{ $receiptVoucher->driver->work_mobile_with_phone_code }}<br>
                    @endif

                    {{-- @if ( --}}
                    {{-- $invoice->clientDetails && --}}
                    {{-- $invoice->clientDetails->company_name && --}}
                    {{-- invoice_setting()->show_client_company_name == 'yes') --}}
                    {{-- {{ $invoice->clientDetails->company_name }}<br> --}}
                    {{-- @endif --}}

                    {{-- @if ( --}}
                    {{-- $invoice->clientDetails && --}}
                    {{-- $invoice->clientDetails->address && --}}
                    {{-- invoice_setting()->show_client_company_address == 'yes') --}}
                    {{-- {!! nl2br($invoice->clientDetails->address) !!} --}}
                    {{-- @endif --}}

                    @endif

                    {{-- @if ($invoiceSetting->show_project == 1 && isset($invoice->project)) --}}
                    {{-- <br><br> --}}
                    {{-- <span class="text-dark-grey text-capitalize">@lang('modules.invoices.projectName')</span><br> --}}
                    {{-- {{ $invoice->project->project_name }} --}}
                    {{-- @endif --}}

                    {{-- @if ($invoiceSetting->show_gst == 'yes' && !is_null($client->clientDetails->gst_number)) --}}
                    {{-- @if ($client->clientDetails->tax_name) --}}
                    {{-- <br>{{$client->clientDetails->tax_name}}: {{$client->clientDetails->gst_number}} --}}
                    {{-- @else --}}
                    {{-- <br>@lang('app.gstIn'): {{ $client->clientDetails->gst_number }} --}}
                    {{-- @endif --}}
                    {{-- @endif --}}
                </p>
            </div>
        </div>

        <div class="items-5">
            <div class="headings">مدينة</div>
            <div class="headings" align="center">رقم القسيمة</div>
            <div class="headings" align="center">رقم حساب</div>
            <div class="headings" align="center">عمل</div>
            <div class="headings" align="right">حساب آخر</div>

            <div>{{ $receiptVoucher->driver->branch->name}}</div>
            <div align="center">{{ $receiptVoucher->driver->iqaama_number }}</div>
            <div align="center">@if(!empty($bussiness))
                {{ $bussiness->platform_id ?: '---' }}
                @else
                ---
                @endif
            </div>
            <div align="center">@if ($receiptVoucher->bussiness)
                {{ $receiptVoucher->business->name ? : '---'}}
                @else
                ---
                @endif
            </div>
            <div align="right">{{ $receiptVoucher->other_business ?: '---' }}</div>
        </div>

        <div class="items-4">
            <div class="headings" align="center">من التاريخ</div>
            <div class="headings" align="center">تاريخ الانتهاء</div>
            <div class="headings" align="right">كمية</div>
            <div class="headings" align="right">مبلغ المحفظة</div>

            <div>{{ $receiptVoucher->start_date->format(company()->date_format) }}</div>
            <div align="center">{{ $receiptVoucher->end_date->format(company()->date_format) }}</div>
            <div align="right">{{ $receiptVoucher->total_amount }}</div>
            <div align="right">{{ $receiptVoucher->wallet_amount }}</div>
        </div>

        <div class="items-3">
            <div class="headings">توقيع المحاسب</div>
            <div class="headings" align="center">توقيع السائق</div>
            <div class="headings" align="right">توقيع المشرف</div>

            <div class="signature-line">________________________________</div>
            <div class="signature-line" align="center">
                @if ($receiptVoucher->signature != "")
                <img src="{{ $receiptVoucher->signature }}" alt="Driver Sign" width="200px" height="100px">
                @else
                ________________________________
                @endif
            </div>
            <div class="signature-line" align="center">{{ $receiptVoucher->creator->name }}</div>
        </div>
    </div>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

<script src="{{ asset('js/NotoKufiArabic-Regular-normal.js') }}"></script>
<script>
    const {
        jsPDF
    } = window.jspdf;
    const content = document.getElementById('content');
    content.classList.add('pdf-style');
    html2canvas(content, {
        scale: 3, // Increase the scale for higher resolution
        useCORS: true // Enable cross-origin for external images
    }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const imgWidth = 210; // A4 width in mm
        const pageHeight = 297; // A4 height in mm
        const imgHeight = canvas.height * imgWidth / canvas.width;
        let heightLeft = imgHeight;
        const doc = new jsPDF('p', 'mm', 'a4');

        let position = 0;
        doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;
        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            doc.addPage();
            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }
        const pdfBlob = doc.output('blob');
        const pdfUrl = URL.createObjectURL(pdfBlob);
        window.open(pdfUrl);
        content.classList.remove('pdf-style');
    });

    setTimeout(() => {
        window.history.back();
    }, 1000);
    // });
</script>

</html>