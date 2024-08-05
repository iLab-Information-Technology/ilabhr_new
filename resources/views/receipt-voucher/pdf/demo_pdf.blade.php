<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Voucher</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

    <style>
        @font-face {
            font-family: 'NotoKufiArabic';
            src: url('{{asset("fonts/NotoKufiArabic-Regular.ttf")}}') format('truetype');
        }

        body {
            font-family: 'NotoKufiArabic', serif;
            font-size: 12px;
            /* Default font size for HTML */
            color: #333;
            margin: 0;
            padding: 0;
            font-weight: normal;
        }

        .invoice-table-wrapper {
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            /* Ensures that the table adjusts based on content */
        }

        th,
        td {
            padding: 8px;/* 
            border: 1px solid #ddd;  */
            vertical-align: middle;
            word-wrap: break-word;
            /* Allows content to wrap within the cell */
        }

        /* Specific widths for each column */
        .col-city {
            width: 20%;
        }

        .col-voucher {
            width: 20%;
        }

        .col-account {
            width: 20%;
        }

        .col-job {
            width: 20%;
        }

        .col-other-account {
            width: 20%;
        }

        .inv-logo-heading img {
            max-width: 150px;
        }

        .inv-logo-heading td {
            vertical-align: top;
        }

        .inv-logo-heading td:nth-child(2) {
            text-align: right;
            font-weight: bold;
            font-size: 21px;
            text-transform: uppercase;
            color: #000;
        }

        .inv-num p {
            margin: 10px 0 0 0;
        }

        .inv-num-date td {
            padding: 5px;
            border: 1px solid #ddd;
        }

        .inv-num-date td:first-child {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .inv-unpaid p {
            margin: 0;
        }

        .inv-unpaid span {
            font-weight: bold;
        }

        .inv-desc td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }

        /* .inv-desc tr:nth-child(even) {
            background-color: #f9f9f9;
        } */

        .inv-desc tr:first-child {
            font-weight: bold;
            color: #333;
            text-align: center;
        }

        .inv-desc tr:last-child td {
            padding: 50px 10px 2px 10px;
        }
        .headings{

            background-color: #f2f2f2;
        }

        .inv-desc-mob {
            display: none;
        }

        @media only screen and (max-width: 600px) {
            .inv-desc {
                display: none;
            }

            .inv-desc-mob {
                display: block;
            }
        }

        .rtl {
            direction: rtl;
        }

        /* PDF-specific styles */
        .pdf-style {
            font-size: 24px;
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
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
    <div class="invoice-table-wrapper" id="content">
        <table>
            <tbody>
                <tr class="inv-logo-heading">
                    <td><img src="{{ asset('user-uploads/app-logo/06eace9938c8f15983f12a5430e8b294.png') }}" alt="ilab Information Technologies" id="logo"></td>
                    <td align="right">سند القبض</td>
                </tr>
                <tr class="inv-num">
                    <td>
                        <p>
                            ilab Information Technologies<br>
                            +966920008946
                        </p>
                    </td>
                    <td align="right">
                        <table class="inv-num-date">
                            <tbody>
                                <tr>
                                    <td>عدد إيصال</td>
                                    <td>{{ $receiptVoucher->voucher_number }}</td>
                                </tr>
                                <tr>
                                    <td>تاريخ استلام</td>
                                    <td>{{ $receiptVoucher->voucher_date->translatedFormat(company()->date_format) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>

        <table>
            <tbody>
                <tr class="inv-unpaid">
                    <td>
                        <p>
                            @if (
                            ($receiptVoucher->driver) &&
                            ($receiptVoucher->driver->name ||
                            $receiptVoucher->driver->email ||
                            $receiptVoucher->driver->work_mobile_no))
                            <span>تم الاستلام من السائق</span><br>
                            @if ($receiptVoucher->driver && $receiptVoucher->driver->name)
                            {{ $receiptVoucher->driver->name }}<br>
                            @endif

                            @if ($receiptVoucher->driver && $receiptVoucher->driver->email)
                            {{ $receiptVoucher->driver->email }}<br>
                            @endif

                            @if ($receiptVoucher->driver && $receiptVoucher->driver->work_mobile_with_phone_code)
                            {{ $receiptVoucher->driver->work_mobile_with_phone_code }}<br>
                            @endif

                            @endif
                        </p>
                    </td> 
                </tr>
                <tr>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>


        <table class="inv-desc">
            <tbody>
                <tr class="headings">
                    <td class="col-city">مدينة</td>
                    <td class="col-voucher" align="center">رقم القسيمة</td>
                    <td class="col-account" align="center">رقم حساب</td>
                    <td class="col-job" align="center">عمل</td>
                    <td class="col-other-account" align="right">حساب آخر</td>
                </tr>
                <tr>
                    <td class="col-city">{{ $receiptVoucher->driver->branch->name}}</td>
                    <td class="col-voucher" align="center">{{ $receiptVoucher->driver->iqaama_number }}</td>
                    <td class="col-account" align="center">@if(!empty($bussiness))
                        {{ $bussiness->platform_id ?: '---' }}
                        @else
                        ---
                        @endif
                    </td>
                    <td class="col-job" align="center">@if ($receiptVoucher->bussiness)
                        {{ $receiptVoucher->business->name ? : '---'}}
                        @else
                        ---
                        @endif
                    </td>
                    <td class="col-other-account" align="right">{{ $receiptVoucher->other_business ?: '---' }}</td>
                </tr>
                <tr class="headings">
                    <td class="col-city" align="center">من التاريخ</td>
                    <td class="col-voucher" align="center">تاريخ الانتهاء</td>
                    <td class="col-account" align="right">كمية</td>
                    <td class="col-job" align="right" colspan="2">مبلغ المحفظة</td>
                </tr>
                <tr>
                    <td class="col-city">{{ $receiptVoucher->start_date->format(company()->date_format) }}</td>
                    <td class="col-voucher" align="center">{{ $receiptVoucher->end_date->format(company()->date_format) }}</td>
                    <td class="col-account" align="right">{{ $receiptVoucher->total_amount }}</td>
                    <td class="col-job" align="right" colspan="2">{{ $receiptVoucher->wallet_amount }}</td>
                </tr>
                <tr class="headings">
                    <td class="col-city">توقيع المحاسب</td>
                    <td class="col-voucher" align="center">توقيع السائق</td>
                    <td class="col-account" align="right" colspan="3">توقيع المشرف</td>
                </tr>
                <tr>
                    <td class="col-city" align="center">________________________________</td>
                    <td class="col-voucher" align="center">@if ($receiptVoucher->signature != "")
                        <img src="{{ $receiptVoucher->signature }}" alt="Driver Sign" width="200px" height="100px">
                        @else ________________________________ @endif
                    </td>
                    <td class="col-account" align="center" colspan="3">{{ $receiptVoucher->creator->name }}</td>
                </tr>
            </tbody>
        </table>

        <div style="float: right; margin-top: 30px;"></div>
    </div>

    <!--   <button id="generatePdf">Generate PDF</button> -->

    <script src="{{ asset('js/NotoKufiArabic-Regular-normal.js') }}"></script>
    <script>
        /*  document.getElementById('generatePdf').addEventListener('click', function () { */
        const {
            jsPDF
        } = window.jspdf;

        // Add the pdf-style class to the content
        const content = document.getElementById('content');
        content.classList.add('pdf-style');

        // Use html2canvas to capture the HTML content as an image
        html2canvas(content, {
            scale: 3, // Increase the scale for higher resolution
            useCORS: true // Enable cross-origin for external images
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 210; // A4 width in mm
            const pageHeight = 297; // A4 height in mm
            const imgHeight = canvas.height * imgWidth / canvas.width;
            let heightLeft = imgHeight;

            // Create a new jsPDF instance
            const doc = new jsPDF('p', 'mm', 'a4');

            let position = 0;

            // Add the image to the PDF
            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            // Add extra pages if needed
            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                doc.addPage();
                doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }
            // Save the PDF
            doc.save("document.pdf");
            // Remove the pdf-style class after generating the PDF
            content.classList.remove('pdf-style');
        });

        // Redirect back after the download starts
        setTimeout(() => {
            window.history.back();
           // window.location.href = 'http://localhost:8085/ilabhr_new/public/account/receipt-voucher';
        }, 1000);
        //  });
        window.top.close();
    </script>
</body>

</html>