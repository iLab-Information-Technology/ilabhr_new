<?php

namespace App\Http\Controllers;

use App\DataTables\ReceiptVoucherDataTable;
use App\DataTables\VoucherDataTable;
use App\Helper\Reply;
use App\Http\Requests\Admin\ReceiptVoucher\StoreRequest;
use App\Models\{Driver, DriverType, User, ReceiptVoucher};
use App\Traits\ImportExcel;
use Illuminate\Http\Request;
use App\Helper\Files;
use App\Http\Requests\Admin\Driver\UpdateRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    public function getDriverType(Request $request){
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
            $voucher = ReceiptVoucher::create($validated);

            DB::commit();
        } catch (\Exception $e) {
            logger($e->getMessage());
            DB::rollback();

            return Reply::error('Some error occurred when inserting the data. Please try again or contact support '. $e->getMessage());
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

        $this->driver = Driver::findOrFail($id);

        $tab = request('tab');

        $this->pageTitle = $this->driver->name;

        switch ($tab) {
            case 'employment':
                $this->view = 'receipt-voucher.ajax.employment';
                break;
            case 'documents':
                $this->view = 'receipt-voucher.ajax.documents';
                break;

            case 'locality':
                $this->view = 'receipt-voucher.ajax.locality';
                $this->countries = countries();
                break;

            case 'banking':
                $this->view = 'receipt-voucher.ajax.banking';
                break;
            case 'businesses':
                return $this->businesses();

            default:
                $this->view = 'receipt-voucher.ajax.profile';
                break;
        }

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['views' => $this->view, 'status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->activeTab = $tab ?: 'profile';

        return view('receipt-voucher.show', $this->data);
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
        $this->driver_types = Driver::all();
        $this->view = 'receipt-voucher.ajax.edit';



        if (request()->ajax()) {
            return view($this->view, $this->data);
        }

        return view('receipt-voucher.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Driver $driver)
    {
        $this->editPermission = user()->permission('edit_receipt_voucher');
        abort_403(!($this->editPermission == 'all'));

        $validated = $request->validated();

        $validated['insurance_expiry_date'] = $request->insurance_expiry_date ? Carbon::createFromFormat($this->company->date_format, $request->insurance_expiry_date)->format('Y-m-d') : null;
        $validated['license_expiry_date'] = $request->license_expiry_date ? Carbon::createFromFormat($this->company->date_format, $request->license_expiry_date)->format('Y-m-d') : null;
        $validated['iqaama_expiry_date'] = $request->iqaama_expiry_date ? Carbon::createFromFormat($this->company->date_format, $request->iqaama_expiry_date)->format('Y-m-d') : null;
        $validated['date_of_birth'] = $request->date_of_birth ? Carbon::createFromFormat($this->company->date_format, $request->date_of_birth)->format('Y-m-d') : null;
        $validated['joining_date'] = $request->joining_date ? Carbon::createFromFormat($this->company->date_format, $request->joining_date)->format('Y-m-d') : null;

        if ($request->iqama_delete == 'yes') {
            Files::deleteFile($driver->iqama, 'iqama');
            $driver->iqama = null;
            $validated['iqaama_expiry_date'] = null;
        }

        if ($request->hasFile('iqama'))
            $validated['iqama'] = Files::uploadLocalOrS3($request->iqama, 'iqama', 300);

        if ($request->license_delete == 'yes') {
            Files::deleteFile($driver->license, 'license');
            $driver->license = null;
            $validated['license_expiry_date'] = null;
        }

        if ($request->hasFile('license'))
            $validated['license'] = Files::uploadLocalOrS3($request->license, 'license', 300);

        if ($request->mobile_form_delete == 'yes') {
            Files::deleteFile($driver->mobile_form, 'mobile_form');
            $driver->mobile_form = null;
        }

        if ($request->hasFile('mobile_form'))
            $validated['mobile_form'] = Files::uploadLocalOrS3($request->mobile_form, 'mobile_form', 300);

        if ($request->sim_form_delete == 'yes') {
            Files::deleteFile($driver->sim_form, 'sim_form');
            $driver->sim_form = null;
        }

        if ($request->hasFile('sim_form'))
            $validated['sim_form'] = Files::uploadLocalOrS3($request->sim_form, 'sim_form', 300);

        if ($request->medical_delete == 'yes') {
            Files::deleteFile($driver->medical, 'medical');
            $driver->medical = null;
        }

        if ($request->hasFile('medical'))
            $validated['medical'] = Files::uploadLocalOrS3($request->medical, 'medical', 300);

        if ($request->other_document_delete == 'yes') {
            Files::deleteFile($driver->other_document, 'other_document');
            $driver->other_document = null;
        }

        if ($request->hasFile('other_document'))
            $validated['other_document'] = Files::uploadLocalOrS3($request->other_document, 'other_document', 300);

        $driver->update($validated);
        return Reply::success(__('messages.updateSuccess'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deletePermission = user()->permission('delete_receipt_voucher');
        abort_403(!($deletePermission == 'all'));

        $this->driver = Driver::findOrFail($id);

        Driver::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }
}
