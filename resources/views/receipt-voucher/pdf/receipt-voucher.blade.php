<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>@lang('app.menu.receipt_voucher') - {{ $receipt_voucher->voucher_number }}</title>
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ $company->favicon_url }}">
    <meta name="theme-color" content="#ffffff">
    @includeIf('invoices.pdf.invoice_pdf_css')
    <style>
        .bg-grey {
            background-color: #F2F4F7;
        }

        .bg-white {
            background-color: #fff;
        }

        .border-radius-25 {
            border-radius: 0.25rem;
        }

        .p-25 {
            padding: 1.25rem;
        }

        .f-11 {
            font-size: 11px;
        }

        .f-12 {
            font-size: 12px;
        }

        .f-13 {
            font-size: 13px;
        }

        .f-14 {
            font-size: 13px;
        }

        .f-15 {
            font-size: 13px;
        }

        .f-21 {
            font-size: 17px;
        }

        .text-black {
            color: #28313c;
        }

        .text-grey {
            color: #616e80;
        }

        .font-weight-700 {
            font-weight: 700;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .text-capitalize {
            text-transform: capitalize;
        }

        .line-height {
            line-height: 15px;
        }

        .mt-1 {
            margin-top: 1rem;
        }

        .mb-0 {
            margin-bottom: 0px;
        }

        .b-collapse {
            border-collapse: collapse;
        }

        .heading-table-left {
            padding: 6px;
            border: 1px solid #DBDBDB;
            font-weight: bold;
            background-color: #f1f1f3;
            border-right: 0;
        }

        .heading-table-right {
            padding: 6px;
            border: 1px solid #DBDBDB;
            border-left: 0;
        }

        .unpaid {
            color: #d30000;
            border: 1px solid #d30000;
            position: relative;
            padding: 5px 10px;
            font-size: 14px;
            border-radius: 0.25rem;
            width: 100px;
            text-align: center;
            margin-top: 50px;
        }

        .other {
            color: #000000;
            border: 1px solid #000000;
            position: relative;
            padding: 5px 10px;
            font-size: 14px;
            border-radius: 0.25rem;
            width: 120px;
            text-align: center;
            margin-top: 50px;
        }

        .paid {
            color: #28a745 !important;
            border: 1px solid #28a745;
            position: relative;
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 0.25rem;
            width: 100px;
            text-align: center;
            margin-top: 50px;
        }

        .main-table-heading {
            border: 1px solid #DBDBDB;
            background-color: #f1f1f3;
            font-weight: 700;
        }

        .main-table-heading td {
            padding: 5px 8px;
            border: 1px solid #DBDBDB;
            font-size: 13px;
        }

        .main-table-items td {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
        }

        .total-box {
            border: 1px solid #e7e9eb;
            padding: 0px;
            border-bottom: 0px;
        }

        .subtotal {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
            border-right: 0;
        }

        .subtotal-amt {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
            border-right: 0;
        }

        .total {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            font-weight: 700;
            border-left: 0;
            border-right: 0;
        }

        .total-amt {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
            border-right: 0;
            font-weight: 700;
        }

        .balance {
            font-size: 14px;
            font-weight: bold;
            background-color: #f1f1f3;
        }

        .balance-left {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
            border-right: 0;
        }

        .balance-right {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
            border-right: 0;
        }

        .centered {
            margin: 0 auto;
        }

        .rightaligned {
            margin-right: 0;
            margin-left: auto;
        }

        .leftaligned {
            margin-left: 0;
            margin-right: auto;
        }

        .page_break {
            page-break-before: always;
        }

        #logo {
            height: 50px;
        }

        .word-break {
            max-width: 175px;
            word-wrap: break-word;
        }

        .summary {
            padding: 11px 10px;
            border: 1px solid #e7e9eb;
            font-size: 11px;
        }

        .border-left-0 {
            border-left: 0 !important;
        }

        .border-right-0 {
            border-right: 0 !important;
        }

        .border-top-0 {
            border-top: 0 !important;
        }

        .border-bottom-0 {
            border-bottom: 0 !important;
        }

        .h3-border {
            border-bottom: 1px solid #AAAAAA;
        }
    </style>
</head>

<body class="content-wrapper">
    <table class="bg-white" border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation">
        <tbody>
            <!-- Table Row Start -->
            <tr>
                <td><img src="{{ $invoiceSetting->logo_url }}" alt="{{ $company->company_name }}" id="logo" /></td>
                <td align="right" class="f-21 text-black font-weight-700 text-uppercase">سند القبض<br>
                    <table class="text-black mt-1 f-11 b-collapse rightaligned">
                        <tr>
                            <td class="heading-table-left">عدد إيصال</td>
                            <td class="heading-table-right">{{ $receipt_voucher->voucher_number }}</td>
                        </tr>
                        {{-- @if ($creditNote)
                    <tr>
                        <td class="heading-table-left">@lang('app.credit-note')</td>
                        <td class="heading-table-right">{{ $creditNote->cn_number }}</td>
                    </tr>
                @endif --}}
                        <tr>
                            <td class="heading-table-left">تاريخ استلام</td>
                            <td class="heading-table-right">
                                {{ $receipt_voucher->voucher_date->translatedFormat(company()->date_format) }}
                            </td>
                        </tr>
                        @if ($receipt_voucher->status === 'unpaid' && $receipt_voucher->end_date->year > 1)
                            <tr>
                                <td class="heading-table-left">تاريخ الاستحقاق</td>
                                <td class="heading-table-right">
                                    {{ $receipt_voucher->end_date->translatedFormat(company()->date_format) }}
                                </td>
                            </tr>
                        @endif
                    </table>
                </td>
            </tr>
            <!-- Table Row End -->
            <!-- Table Row Start -->
            {{-- <tr> --}}
                {{-- <td class="f-12 text-black"> --}}
                    {{-- <p class="line-height mb-0 "> --}}
                        {{-- <span class="text-grey text-capitalize">@lang('modules.invoices.billedFrom')</span><br> --}}
                        {{-- {{ $company->company_name }}<br>
                        @if ($company->company_email)
                            {{ $company->company_email }}<br>
                        @endif

                        @if ($company->company_phone)
                            {{ $company->company_phone }}<br>
                        @endif

                        @if (!is_null($company) && $invoice->address)
                            {!! nl2br($invoice->address->address) !!}<br>
                        @endif

                        @if ($invoiceSetting->show_gst == 'yes' && $invoice->address->tax_number)
                            {{ $invoice->address->tax_name }}: {{ $invoice->address->tax_number }}
                        @endif --}}
                    {{-- </p> --}}
                    {{-- @if ($invoiceSetting->show_project == 1 && isset($invoice->project->project_name))
                        <br>
                        <p class="line-height mb-0"></p>
                    @endif --}}
                {{-- </td> --}}
                <td class="f-12 text-black">
                    {{-- @if (
                        !is_null($invoice->project) &&
                            !is_null($invoice->project->client) &&
                            !is_null($invoice->project->client->clientDetails))
                        @php
                            $client = $invoice->project->client;
                        @endphp
                    @elseif(!is_null($invoice->client_id) && !is_null($invoice->clientDetails))
                        @php
                            $client = $invoice->client;
                        @endphp
                    @endif --}}

                    <p class="line-height mb-0">
                        <span class="text-grey text-capitalize">
                            تمت إرسال الفاتورة إلى</span><br>

                        @if ($receipt_voucher->driver && $receipt_voucher->driver->name)
                            {{ $receipt_voucher->driver->name }}<br>
                        @endif

                        @if ($receipt_voucher->driver && $receipt_voucher->driver->email)
                            {{ $receipt_voucher->driver->email }}<br>
                        @endif

                        @if ($receipt_voucher->driver && $receipt_voucher->driver->work_mobile_with_phone_code)
                            {{ $receipt_voucher->driver->work_mobile_with_phone_code }}<br>
                        @endif

                        {{-- @if ($client->clientDetails->company_name && $invoiceSetting->show_client_company_name == 'yes')
                                {{ $client->clientDetails->company_name }}<br>
                            @endif --}}

                        {{-- @if ($client->clientDetails->address && $invoiceSetting->show_client_company_address == 'yes')
                                {!! nl2br($client->clientDetails->address) !!}
                            @endif --}}
                        {{-- 
                            @if ($invoiceSetting->show_gst == 'yes' && !is_null($client->clientDetails->gst_number))
                                @if ($client->clientDetails->tax_name)
                                    <br>{{ $client->clientDetails->tax_name }}:
                                    {{ $client->clientDetails->gst_number }}
                                @else
                                    <br>@lang('app.gstIn'): {{ $client->clientDetails->gst_number }}
                                @endif
                            @endif --}}
                    </p>

                    {{-- @if ($invoiceSetting->show_project == 1 && isset($invoice->project->project_name))
                        <br>
                        <p class="line-height mb-0">
                            <span class="text-grey text-capitalize">@lang('modules.invoices.projectName')</span>:
                            {{ $invoice->project->project_name }}
                        </p>
                    @endif --}}

                </td>
            </tr>
            <!-- Table Row End -->
            <!-- Table Row Start -->
            <tr>
                <td height="10"></td>
            </tr>
            <!-- Table Row End -->
            <!-- Table Row Start -->
            <tr>
                <td colspan="2">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td class="f-14 text-black">
                                {{-- @if ($invoice->show_shipping_address == 'yes')
                                    <p class="line-height"><span
                                            class="text-grey text-capitalize">@lang('app.shippingAddress')</span><br>
                                        {!! nl2br($client->clientDetails->shipping_address) !!}</p>
                                @endif --}}
                            </td>
                            {{-- @if ($invoiceSetting->show_status)
                                <td align="right">
                                    <div style="margin: 0 0 auto auto"
                                        class="text-uppercase bg-white {{ $invoice->status == 'paid' || $invoice->status == 'unpaid' ? $invoice->status : 'other' }} rightaligned">
                                        @if ($invoice->credit_note)
                                            @lang('app.credit-note')
                                        @else
                                            @lang('modules.invoices.' . $invoice->status)
                                        @endif
                                    </div>
                                </td>
                            @endif --}}
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <table width="100%" class="f-14 b-collapse">
        <tr>
            <td height="10" colspan="2"></td>
        </tr>
        <!-- Table Row Start -->
        <tr class="main-table-heading text-grey">
            <td>مدينة</td>
            <td width="20%">رقم الاقامة</td>
            <td>رقم حساب</td>
            <td>عمل</td>
            <td align="right">حساب آخر</td>
        </tr>
        <!-- Table Row End -->
        <!-- Table Row Start -->
        <tr class="f-12 main-table-items text-black">
            <td class="border-bottom-0">
                {{ $receipt_voucher->driver->branch->name }}
            </td>
            <td width="10%" class="border-bottom-0">
                {{ $receipt_voucher->driver->iqaama_number }}
            </td>
            <td class="border-bottom-0">
                {{ $bussiness->platform_id ?: '---' }}
            </td>
            <td class="border-bottom-0">
                {{ $receipt_voucher->business->name ?: '---' }}
            </td>
            <td align="right" class="border-bottom-0">
                {{ $receipt_voucher->other_business ?: '---' }}
            </td>
        </tr>
        <tr class="main-table-heading text-grey">
            <td>من التاريخ</td>
            <td>ان يذهب في موعد</td>
            <td align="right" colspan="3">المبلغ الإجمالي</td>
        </tr>
        <tr class="f-12 main-table-items text-black">
            <td class="border-bottom-0">
                {{ $receipt_voucher->start_date->format(company()->date_format) }}
            </td>
            <td width="10%" class="border-bottom-0">
                {{ $receipt_voucher->end_date->format(company()->date_format) }}
            </td>
            <td align="right" class="border-bottom-0" colspan="3">
                {{ $receipt_voucher->total_amount }}
            </td>
        </tr>
        <tr class="main-table-heading text-grey">
            <td>توقيع المحاسب</td>
            <td>توقيع السائق</td>
            <td align="right" colspan="3">توقيع المشرف</td>
        </tr>
        <tr class="f-12 main-table-items text-black">
            <td style="padding: 50px 10px 2px 10px" align="center">
                ________________________________
            </td>
            <td style="padding: 50px 10px 2px 10px" align="center">
                ________________________________
            </td>
            <td style="padding: 50px 10px 2px 10px" align="center" colspan="3">
                ________________________________
            </td>
        </tr>
    </table>

</body>

</html>
