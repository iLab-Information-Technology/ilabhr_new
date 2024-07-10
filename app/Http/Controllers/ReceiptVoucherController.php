<?php

namespace App\Http\Controllers;

use App\DataTables\ReceiptVoucherDataTable;
use App\DataTables\VoucherDataTable;
use App\Helper\Reply;
use App\Http\Requests\Admin\ReceiptVoucher\StoreRequest;
use App\Http\Requests\Admin\ReceiptVoucher\UpdateRequest;
use App\Models\{Business, Driver, DriverType, User, ReceiptVoucher};
use App\Traits\ImportExcel;
use Illuminate\Http\Request;
use App\Helper\Files;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;

class ReceiptVoucherController extends AccountBaseController
{
    use ImportExcel;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.receipt_voucher';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('receiptVoucher', $this->user->modules));
            return $next($request);
        });
    }

    public function getDriverType(Request $request)
    {
        return DriverType::find($request->id);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(VoucherDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_receipt_voucher');
        abort_403(!in_array($viewPermission, ['all']));

        return $dataTable->render('receipt-voucher.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $addPermission = user()->permission('add_receipt_voucher');
        abort_403(!in_array($addPermission, ['all', 'added']));

        $this->pageTitle = __('app.addReceiptVoucher');
        $this->countries = countries();
        $this->view = 'receipt-voucher.ajax.create';
        $this->drivers = Driver::all();
        $lastVoucherId = ReceiptVoucher::orderBy('id', 'desc')->pluck('voucher_number')->first();
        $this->lastVoucherId = $lastVoucherId == 0 ? 1000 : $lastVoucherId + 1;
        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('receipt-voucher.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $addPermission = user()->permission('add_receipt_voucher');
        abort_403(!in_array($addPermission, ['all', 'added']));
        DB::beginTransaction();
        try {

            $validated = $request->validated();
            $validated['voucher_date'] = Carbon::createFromFormat($this->company->date_format, $request->voucher_date)->format('Y-m-d');
            $validated['start_date'] = Carbon::createFromFormat($this->company->date_format, $request->start_date)->format('Y-m-d');
            $validated['end_date'] = Carbon::createFromFormat($this->company->date_format, $request->end_date)->format('Y-m-d');
            $validated['business_id'] = $request->business_id ? $request->business_id : 0;
            $validated['other_business'] = $request->other_business ? $request->other_business : '';

            $voucher = ReceiptVoucher::create($validated);


            DB::commit();
        } catch (\Exception $e) {
            logger($e->getMessage());
            DB::rollback();

            return Reply::error('Some error occurred when inserting the data. Please try again or contact support ' . $e->getMessage());
        }

        if (request()->add_more == 'true') {
            $html = $this->create();
            return Reply::successWithData(__('messages.recordSaved'), ['html' => $html, 'add_more' => true]);
        }

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('receipt-voucher.index')]);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->viewPermission = user()->permission('view_receipt_voucher');
        abort_403(!($this->viewPermission == 'all'));

        $this->receiptVoucher = ReceiptVoucher::with('driver', 'business')->findOrFail($id);

        $this->receiptVoucherFirst = ReceiptVoucher::with('driver')->first();

        $this->bussiness = DB::table('business_driver')->where([
            'driver_id' => $this->receiptVoucher->driver_id,
            'business_id' => $this->receiptVoucher->business_id,
        ])->first();


        $tab = request('tab');

        $this->pageTitle = $this->receiptVoucher->driver->name;
        $this->view = 'receipt-voucher.ajax.show';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();
            return Reply::dataOnly(['views' => $this->view, 'status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->activeTab = $tab ?: 'profile';

        return view('receipt-voucher.create', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReceiptVoucher $receiptVoucher)
    {
        $this->editPermission = user()->permission('edit_receipt_voucher');
        abort_403(!($this->editPermission == 'all'));

        $this->pageTitle = __('app.update');

        $this->receiptVoucher = $receiptVoucher;
        $this->drivers = Driver::all();
        $this->view = 'receipt-voucher.ajax.edit';

        if (request()->ajax()) {
            return view('receipt-voucher.ajax.edit', $this->data);
        }

        return view('receipt-voucher.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, ReceiptVoucher $receiptVoucher)
    {
        $this->editPermission = user()->permission('edit_receipt_voucher');
        abort_403(!($this->editPermission == 'all'));
        $validated = $request->validated();
        $validated['voucher_date'] = Carbon::createFromFormat($this->company->date_format, $request->voucher_date)->format('Y-m-d');
        $validated['start_date'] = Carbon::createFromFormat($this->company->date_format, $request->start_date)->format('Y-m-d');
        $validated['end_date'] = Carbon::createFromFormat($this->company->date_format, $request->end_date)->format('Y-m-d');
        $validated['business_id'] = $request->business_id ? $request->business_id : 0;
        $validated['other_business'] = $request->other_business ? $request->other_business : '';
        $receiptVoucher->update($validated);

        return Reply::success(__('messages.updateSuccess'), ['redirectUrl' => route('receipt-voucher.index')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deletePermission = user()->permission('delete_receipt_voucher');
        abort_403(!($deletePermission == 'all'));

        $this->receiptVoucher = ReceiptVoucher::findOrFail($id);

        ReceiptVoucher::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function download($id)
    {
        $this->invoiceSetting = invoice_setting();
        $this->receipt_voucher = ReceiptVoucher::with('driver', 'business')->findOrFail($id);

        // if ($this->invoice->getCustomFieldGroupsWithFields()) {
            // $this->fields = $this->invoice->getCustomFieldGroupsWithFields()->fields;
        // }

        $this->viewPermission = user()->permission('view_invoices');
        // $this->company = $this->invoice->company;

        $viewProjectInvoicePermission = user()->permission('view_project_invoices');
        abort_403(!(
            $this->viewPermission == 'all'
            || ($this->viewPermission == 'added' && $this->invoice->added_by == user()->id)
            || ($this->viewPermission == 'owned' && $this->invoice->client_id == user()->id)
            || ($viewProjectInvoicePermission == 'owned' && $this->invoice->project_id && $this->invoice->project->client_id == user()->id)
        ));

        // App::setLocale($this->invoiceSetting->locale);
        // Carbon::setLocale($this->invoiceSetting->locale);

        // Download file uploaded
        if ($this->receipt_voucher->file != null) {
            return response()->download(storage_path('app/public/receipt-files') . '/' . $this->invoice->file);
        }

        $pdfOption = $this->domPdfObjectForDownload($id);
        $pdf = $pdfOption['pdf'];
        $filename = $pdfOption['fileName'];

        return request()->view ? $pdf->stream($filename . '.pdf') : $pdf->download($filename . '.pdf');
    }

    public function domPdfObjectForDownload($id)
    {
        $this->invoiceSetting = invoice_setting();
        $this->receipt_voucher = ReceiptVoucher::with('driver', 'business')->findOrFail($id);

        $this->bussiness = DB::table('business_driver')->where([
            'driver_id' => $this->receipt_voucher->driver_id,
            'business_id' => $this->receipt_voucher->business_id,
        ])->first();



        // App::setLocale($this->invoiceSetting->locale);
        // Carbon::setLocale($this->invoiceSetting->locale);
        // $this->paidAmount = $this->invoice->getPaidAmount();
        // $this->creditNote = 0;

        // if ($this->invoice->getCustomFieldGroupsWithFields()) {
            // $this->fields = $this->invoice->getCustomFieldGroupsWithFields()->fields;
        // }

        // if ($this->invoice->credit_note) {
            // $this->creditNote = CreditNotes::where('invoice_id', $id)
                // ->select('cn_number')
                // ->first();
        // }

        // $this->discount = 0;

        // if ($this->invoice->discount > 0) {
            // if ($this->invoice->discount_type == 'percent') {
                // $this->discount = (($this->invoice->discount / 100) * $this->invoice->sub_total);
            // }
            // else {
                // $this->discount = $this->invoice->discount;
            // }
        // }

        // $taxList = array();

        // $items = InvoiceItems::whereNotNull('taxes')->where('invoice_id', $this->invoice->id)->get();

        // foreach ($items as $item) {

            // foreach (json_decode($item->taxes) as $tax) {
                // $this->tax = InvoiceItems::taxbyid($tax)->first();

        //         if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {

        //             if ($this->invoice->calculate_tax == 'after_discount' && $this->discount > 0) {
        //                 $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($item->amount - ($item->amount / $this->invoice->sub_total) * $this->discount) * ($this->tax->rate_percent / 100);

        //             }
        //             else {
        //                 $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $item->amount * ($this->tax->rate_percent / 100);
        //             }

        //         }
        //         else {
        //             if ($this->invoice->calculate_tax == 'after_discount' && $this->discount > 0) {
        //                 $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($item->amount - ($item->amount / $this->invoice->sub_total) * $this->discount) * ($this->tax->rate_percent / 100));

        //             }
        //             else {
        //                 $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + ($item->amount * ($this->tax->rate_percent / 100));
        //             }
        //         }
        //     }
        // }

        // $this->taxes = $taxList;

        // $this->company = $this->invoice->company;

        // $this->invoiceSetting = $this->company->invoiceSetting;

        // $this->payments = Payment::with(['offlineMethod'])->where('invoice_id', $this->invoice->id)->where('status', 'complete')->orderBy('paid_on', 'desc')->get();

        $pdf = app('dompdf.wrapper');
        $pdf->setOption('enable_php', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        $pdf->loadView('receipt-voucher.pdf.receipt-voucher', $this->data);
        $filename = $this->receipt_voucher->voucher_number;

        return [
            'pdf' => $pdf,
            'fileName' => $filename
        ];
    }

}
