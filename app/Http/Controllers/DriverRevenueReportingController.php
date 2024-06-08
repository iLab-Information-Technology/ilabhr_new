<?php

namespace App\Http\Controllers;

use App\DataTables\BusinessesDriverDataTable;
use App\DataTables\DriversPayrollDataTable;
use App\Helper\Reply;
use App\Http\Requests\Admin\Driver\StoreRequest;
use App\Models\{Driver, DriverType, Business};
use App\Traits\ImportExcel;
use Illuminate\Http\Request;
use App\Helper\Files;
use App\Http\Requests\Admin\Driver\UpdateRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DriverRevenueReportingController extends AccountBaseController
{
    use ImportExcel;

    public function __construct(private DriversPayrollDataTable $driversPayrollDataTable)
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.revenue_reporting';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('drivers', $this->user->modules));
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $viewPermission = user()->permission('view_drivers');
        // abort_403(!in_array($viewPermission, ['all']));

        $now = now();
        $this->year = $now->format('Y');
        $this->month = $now->format('m');
        $this->businesses = Business::all();
        return $this->driversPayrollDataTable->render('drivers-revenue.index', $this->data);
    }
}
