<?php

namespace App\Http\Controllers;

use App\DataTables\BusinessesDriverDataTable;
use App\DataTables\DriversRevenueReportDataTable;
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

    public function __construct(private DriversRevenueReportDataTable $driverRevenueReportDataTable)
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
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $this->businesses = Business::with([
            'coordinator_reports' => function($query) use ($currentMonth, $currentYear) {
                $query
                    // Uncomment these lines if you want to filter by current month and year
                    // ->whereMonth('report_date', $currentMonth)
                    // ->whereYear('report_date', $currentYear)
                    ->with(['field_values' => function($query) {
                        $query->where('field_id', 1);
                    }]);
            }
        ])->get();

        foreach ($this->businesses as $business) {
            $totalSum = 0;
            foreach ($business->coordinator_reports as $report) {
                $totalSum += $report->field_values->sum('value');
            }
            $business->total_orders = $totalSum;
        }

        $drivers = Driver::with([
            'branch',
            'driver_type',
            'coordinator_reports' => function($query) use ($currentMonth, $currentYear) {
                $query
                    // ->whereMonth('report_date', $currentMonth)
                    // ->whereYear('report_date', $currentYear)
                    ->with(['field_values' => function($query) {
                        $query->where('field_id', 1);
                    }]);
            }
        ])->get([
            'id',
            'name',
            'iqaama_number',
            'branch_id',
            'driver_type_id',
            'fuel',
            'gprs',
            'government_cost',
            'accommodation',
            'vehicle_monthly_cost',
            'mobile_data'
        ]);
        $this->total_cost = 0;
        $this->total_orders = 0;
        foreach ($drivers as $driver) {
            $totalSum = 0;
            foreach ($driver->coordinator_reports as $report) {
                $totalSum += $report->field_values->sum('value');
            }
            $driver->total_orders = $totalSum;

            // Sum of specific fields from the driver
            $driver->per_month_orders = 250;
            $driver->per_month_salary = 400;
            $driver->per_day_orders = 250 / 26;
            $driver->per_day_salary = 400 / 26;
            $driver->total_salary =  $this->calculate_driver_order_price($driver->total_orders, 26, true);
            $total_coordinate_days = $driver->coordinator_reports->count();
            $total_gprs = $driver->gprs / $total_coordinate_days;
            $total_fuel = $driver->fuel / $total_coordinate_days;
            $total_government_cost = $driver->government_cost / $total_coordinate_days;
            $total_accommodation = $driver->accommodation / $total_coordinate_days;
            $total_vehicle_monthly_cost = $driver->vehicle_monthly_cost / $total_coordinate_days;
            $total_mobile_data = $driver->mobile_data / $total_coordinate_days;

            $driver->total_cost = $driver->total_salary + $total_gprs + $total_fuel + $total_government_cost + $total_accommodation + $total_vehicle_monthly_cost + $total_mobile_data;
            $this->total_cost += $driver->total_cost;
            $this->total_orders += $driver->total_orders;
        }
        // return $drivers;
        return $this->driverRevenueReportDataTable->render('drivers-revenue.index', $this->data);
    }

    private function calculate_base_salary($working_days, $freelancer, $BASE_SALARY_PER_MONTH, $WORKING_DAYS_PER_MONTH) {
        if ($freelancer) {
            return ($BASE_SALARY_PER_MONTH / $WORKING_DAYS_PER_MONTH) * min($working_days, $WORKING_DAYS_PER_MONTH);
        }
        return $BASE_SALARY_PER_MONTH;
    }

    private function calculate_base_order_limit($working_days, $freelancer, $BASE_ORDER_LIMIT_PER_MONTH, $WORKING_DAYS_PER_MONTH) {
        if ($freelancer) {
            return ($BASE_ORDER_LIMIT_PER_MONTH / $WORKING_DAYS_PER_MONTH) * min($working_days, $WORKING_DAYS_PER_MONTH);
        }
        return $BASE_ORDER_LIMIT_PER_MONTH;
    }

    public function calculate_driver_order_price($total_order, $working_days, $freelancer) {
        $WORKING_DAYS_PER_MONTH = 26;
        $BASE_SALARY_PER_MONTH = 400;
        $BASE_ORDER_LIMIT_PER_MONTH = 250;
        $COMMISSION_RATE = 9;

        $base_salary = $this->calculate_base_salary($working_days, $freelancer, $BASE_SALARY_PER_MONTH, $WORKING_DAYS_PER_MONTH);
        $base_order_limit = $this->calculate_base_order_limit($working_days, $freelancer, $BASE_ORDER_LIMIT_PER_MONTH, $WORKING_DAYS_PER_MONTH);
        $per_order_base_salary = $base_salary / $base_order_limit;

        if ($total_order <= $base_order_limit) {
            $base_salary = $per_order_base_salary * $total_order;
            $deductions = $per_order_base_salary * ($base_order_limit - $total_order);
            $commission_amount = 0;
        } else {
            $deductions = 0;
            $commission_amount = ($total_order - $base_order_limit) * $COMMISSION_RATE;
        }

        $final_salary = ($base_salary + $commission_amount) - $deductions;

        return $final_salary;
    }

}
