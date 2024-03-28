<?php

namespace App\Http\Controllers;

use App\DataTables\BusinessesDriverDataTable;
use App\Enums\Salutation;
use App\Helper\Reply;
use App\Http\Requests\Admin\DriverEmployee\StoreRequest;
use App\Http\Requests\Admin\DriverEmployee\UpdateRequest;
use App\Models\Driver;
use App\Models\Business;
use App\Models\BusinessDriver;
use App\Models\LanguageSetting;
use App\Models\Role;
use App\Models\User;
use App\Traits\ImportExcel;
use Illuminate\Support\Facades\DB;

class DriverEmployeeController extends AccountBaseController
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
    public function index(Driver $driver)
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(User $employee)
    {
        $this->pageTitle = __('app.addDriver');

        // $addPermission = user()->permission('add_employees');
        // abort_403(!in_array($addPermission, ['all', 'added']));


        $this->employee = $employee;

        $userRoles = user()->roles->pluck('name')->toArray();

        if(in_array('admin', $userRoles))
        {
            $this->roles = Role::where('name', '<>', 'client')->get();
        }
        else
        {
            $this->roles = Role::whereNotIn('name', ['admin', 'client'])->get();
        }

        $this->view = 'employees.drivers.ajax.create';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('employees.drivers.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request, User $employee)
    {
        // $addPermission = user()->permission('add_employees');
        // abort_403(!in_array($addPermission, ['all', 'added']));

        // WORKSUITESAAS
        $company = company();

        if ($employee->drivers()->where('driver_employee.driver_id', $request->driver_id)->exists()) {
            return Reply::error(__('messages.driverExists'));
        }

        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $employee->drivers()->attach($validated['driver_id']);
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
            $html = $this->create($employee);

            return Reply::successWithData(__('messages.recordSaved'), ['html' => $html, 'add_more' => true]);
        }

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('employees.show', $employee->id) . '?tab=link-drivers']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
      
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Driver $driver, Business $business)
    {
       
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $employee, Driver $driver)
    {
        $this->discussion = $employee->drivers()->detach($driver->id);

        return Reply::success(__('messages.deleteSuccess'));
    }
}
