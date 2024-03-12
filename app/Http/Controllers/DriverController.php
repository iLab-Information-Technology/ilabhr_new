<?php

namespace App\Http\Controllers;

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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
