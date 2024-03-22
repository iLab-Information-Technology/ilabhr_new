<?php

namespace App\Http\Controllers;

use App\DataTables\BusinessesDataTable;
use App\Enums\Salutation;
use App\Helper\Reply;
use App\Http\Requests\Admin\Business\StoreRequest;
use App\Models\Driver;
use App\Models\LanguageSetting;
use App\Models\Role;
use App\Traits\ImportExcel;
use Illuminate\Http\Request;
use App\Helper\Files;
use App\Models\Business;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BusinessController extends AccountBaseController
{
    use ImportExcel;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.businesses';
        $this->middleware(function ($request, $next) {
            // abort_403(!in_array('driv', $this->user->modules));

            return $next($request);
        });
    }

    public function ajaxLoadBusiness(Request $request)
    {
        $search = $request->search;

        $businesses = Business::orderby('name')
            ->select('id', 'name')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->take(20)
            ->get();

        $response = array();

        foreach ($businesses as $business) {

            $response[] = array(
                'id' => $business->id,
                'text' => $business->name
            );

        }

        return response()->json($response);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(BusinessesDataTable $dataTable)
    {
        return $dataTable->render('businesses.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->pageTitle = __('app.addProject');

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

        $this->view = 'businesses.ajax.create';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('businesses.create', $this->data);
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

            Business::create($validated);

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

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('businesses.index')]);
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
        $this->discussion = Business::findOrFail($id);
        // $deletePermission = user()->permission('delete_project_discussions');
        // abort_403(!($deletePermission == 'all' || ($deletePermission == 'added' && $this->discussion->added_by == user()->id)));

        Business::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }
}
