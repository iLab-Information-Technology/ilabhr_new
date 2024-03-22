<?php

namespace App\Http\Controllers;

use App\DataTables\BusinessesDriverDataTable;
use App\DataTables\DriversDataTable;
use App\Enums\Salutation;
use App\Helper\Reply;
use App\Http\Requests\Admin\Driver\StoreRequest;
use App\Models\Driver;
use App\Models\LanguageSetting;
use App\Models\Role;
use App\Traits\ImportExcel;
use Illuminate\Http\Request;
use App\Helper\Files;
use App\Http\Requests\Admin\Driver\UpdateRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DriverController extends AccountBaseController
{
    use ImportExcel;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.drivers';
        $this->middleware(function ($request, $next) {
            // abort_403(!in_array('driv', $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(DriversDataTable $dataTable)
    {
        return $dataTable->render('drivers.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->pageTitle = __('app.addDriver');

        $addPermission = user()->permission('add_employees');
        abort_403(!in_array($addPermission, ['all', 'added']));


        $this->teams = []; // Team::all();
        $this->designations = []; // Designation::allDesignations();

        $this->skills = []; // Skill::all()->pluck('name')->toArray();
        $this->countries = countries();
        $this->lastEmployeeID = 0; // EmployeeDetails::count();
        $this->checkifExistEmployeeId = []; // EmployeeDetails::select('id')->where('employee_id', ($this->lastEmployeeID + 1))->first();
        $this->employees = []; // User::allEmployees(null, true);
        $this->languages = LanguageSetting::where('status', 'enabled')->get();
        $this->salutations = Salutation::cases();

        $userRoles = user()->roles->pluck('name')->toArray();

        if(in_array('admin', $userRoles))
        {
            $this->roles = Role::where('name', '<>', 'client')->get();
        }
        else
        {
            $this->roles = Role::whereNotIn('name', ['admin', 'client'])->get();
        }

        $this->view = 'drivers.ajax.create';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('drivers.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        // $addPermission = user()->permission('add_employees');
        // abort_403(!in_array($addPermission, ['all', 'added']));

        // WORKSUITESAAS
        $company = company();

        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $validated['insurance_expiry_date'] = $request->insurance_expiry_date ? Carbon::createFromFormat($this->company->date_format, $request->insurance_expiry_date)->format('Y-m-d') : null;
            $validated['license_expiry_date'] = $request->license_expiry_date ? Carbon::createFromFormat($this->company->date_format, $request->license_expiry_date)->format('Y-m-d') : null;
            $validated['iqaama_expiry_date'] = $request->iqaama_expiry_date ? Carbon::createFromFormat($this->company->date_format, $request->iqaama_expiry_date)->format('Y-m-d') : null;
            $validated['date_of_birth'] = $request->date_of_birth ? Carbon::createFromFormat($this->company->date_format, $request->date_of_birth)->format('Y-m-d') : null;
            $validated['work_mobile_no'] = '+' . $request->work_mobile_country_code . $request->work_mobile_no;

            unset($validated['work_mobile_country_code']);

            if ($request->hasFile('image')) {
                $validated['image'] = Files::uploadLocalOrS3($request->image, 'avatar', 300);
            }
            
            Driver::create($validated);
            // Commit Transaction
            DB::commit();

            // WORKSUITESAAS
            session()->forget('company');

        } catch (\Exception $e) {
            logger($e->getMessage());
            // Rollback Transaction
            DB::rollback();

            return Reply::error('Some error occurred when inserting the data. Please try again or contact support '. $e->getMessage());
        }


        if (request()->add_more == 'true') {
            $html = $this->create();

            return Reply::successWithData(__('messages.recordSaved'), ['html' => $html, 'add_more' => true]);
        }

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('drivers.index')]);
    }


    public function businesses()
    {

        // $viewPermission = user()->permission('view_employee_projects');
        // abort_403(!in_array($viewPermission, ['all']));

        $tab = request('tab');
        $this->activeTab = $tab ?: 'businesses';
        $this->view = 'drivers.ajax.businesses';

        $dataTable = new BusinessesDriverDataTable();

        return $dataTable->render('drivers.show', $this->data);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->viewPermission = user()->permission('view_employees');

        $this->driver = Driver::findOrFail($id);

        // abort_403(in_array('client', user_roles()));

        $tab = request('tab');

        $this->pageTitle = $this->driver->name;
        // $viewDocumentPermission = user()->permission('view_documents');

        switch ($tab) {
            case 'employment':
                $this->view = 'drivers.ajax.employment';
                break;
            case 'documents':
                // abort_403(($viewDocumentPermission == 'none'));
                $this->view = 'drivers.ajax.documents';
                break;

            case 'locality':
                // abort_403(($viewDocumentPermission == 'none'));
                $this->view = 'drivers.ajax.locality';
                $this->countries = countries();
                break;

            case 'banking':
                // abort_403(($viewDocumentPermission == 'none'));
                $this->view = 'drivers.ajax.banking';
                break;
            case 'businesses':
                // abort_403(($viewDocumentPermission == 'none'));
                return $this->businesses();

            default:
                $this->view = 'drivers.ajax.profile';
                break;
        }

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['views' => $this->view, 'status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->activeTab = $tab ?: 'profile';

        return view('drivers.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Driver $driver)
    {
        $this->pageTitle = _('app.update');
        $this->driver = $driver;
        $tab = request('tab');

        switch($tab) {
            case 'iqama':
                $this->view = 'drivers.ajax.iqama-modal';
                break;
            case 'license':
                $this->view = 'drivers.ajax.license-modal';
                break;
            case 'sim-form':
                $this->view = 'drivers.ajax.sim-form-modal';
                break;
            case 'mobile-form':
                $this->view = 'drivers.ajax.mobile-form-modal';
                break;
            case 'medical':
                $this->view = 'drivers.ajax.medical-modal';
                break;
            case 'other-document':
                $this->view = 'drivers.ajax.other-document-modal';
                break;
        }

        if (request()->ajax()) {
            return view($this->view, $this->data);
        }
        
        return view('drivers.ajax.iqama-modal', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Driver $driver)
    {
        $validated = $request->validated();
        
        $validated['joining_date'] = $request->joining_date ? Carbon::createFromFormat($this->company->date_format, $request->joining_date)->format('Y-m-d') : $driver->joining_date;
        $validated['insurance_expiry_date'] = $request->insurance_expiry_date ? Carbon::createFromFormat($this->company->date_format, $request->insurance_expiry_date)->format('Y-m-d') : $driver->insurance_expiry_date;
        $validated['license_expiry_date'] = $request->license_expiry_date ? Carbon::createFromFormat($this->company->date_format, $request->license_expiry_date)->format('Y-m-d') : $driver->license_expiry_date;
        $validated['iqaama_expiry_date'] = $request->iqaama_expiry_date ? Carbon::createFromFormat($this->company->date_format, $request->iqaama_expiry_date)->format('Y-m-d') : $driver->iqaama_expiry_date;

        if ($request->hasFile('iqama'))
            $validated['iqama'] = Files::uploadLocalOrS3($request->iqama, 'iqama', 300);
        
        if ($request->hasFile('license'))
            $validated['license'] = Files::uploadLocalOrS3($request->license, 'license', 300);
        
        if ($request->hasFile('mobile_form'))
            $validated['mobile_form'] = Files::uploadLocalOrS3($request->mobile_form, 'mobile_form', 300);

        if ($request->hasFile('sim_form'))
            $validated['sim_form'] = Files::uploadLocalOrS3($request->sim_form, 'sim_form', 300);

        if ($request->hasFile('medical'))
            $validated['medical'] = Files::uploadLocalOrS3($request->medical, 'medical', 300);

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
        $this->discussion = Driver::findOrFail($id);
        // $deletePermission = user()->permission('delete_project_discussions');
        // abort_403(!($deletePermission == 'all' || ($deletePermission == 'added' && $this->discussion->added_by == user()->id)));

        Driver::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }
}
