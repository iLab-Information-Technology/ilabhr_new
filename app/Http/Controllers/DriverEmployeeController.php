<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Http\Requests\Admin\DriverEmployee\StoreRequest;
use App\Models\Driver;
use App\Models\Business;
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
            abort_403(!in_array('employees', $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(User $employee)
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(User $employee)
    {
        $addPermission = $employee->permission('add_linked_drivers');
        abort_403(!in_array($addPermission, ['all', 'added']));

        $this->pageTitle = __('app.addDriver');
        $this->employee = $employee;
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
        $addPermission = $employee->permission('add_linked_drivers');
        abort_403(!in_array($addPermission, ['all', 'added']));

        if ($employee->drivers()->where('driver_employee.driver_id', $request->driver_id)->exists()) {
            return Reply::error(__('messages.driverExists'));
        }

        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $employee->drivers()->attach($validated['driver_id']);

            DB::commit();
        } catch (\Exception $e) {
            logger($e->getMessage());
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
        $deletePermission = $employee->permission('delete_linked_drivers');
        abort_403(!($deletePermission == 'all'));

        $this->linkedDriver = $employee->drivers()->detach($driver->id);

        return Reply::success(__('messages.deleteSuccess'));
    }
}
