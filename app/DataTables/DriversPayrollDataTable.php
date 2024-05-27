<?php

namespace App\DataTables;

use App\Models\Business;
use App\Models\CoordinatorReport;
use App\Models\Driver;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class DriversPayrollDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('contract_type', function ($row) {
                return ucwords(strtolower($row->contract_type));
            })
            ->addColumn('nationality', function ($row) {
                return ucwords(strtolower($row->nationality));
            });
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(CoordinatorReport $model): QueryBuilder
    {
        $request = $this->request();
        $WORKING_DAYS_PER_MONTH = 26;
        $BASE_SALARY_PER_MONTH = 400;
        $BASE_ORDER_LIMIT_PER_MONTH = 250;
        $COMMISSION_RATE = 9;
        $businesses = Business::select('id', 'name')->get();
        $businessOrders = array_map(function ($b) {
            return DB::raw("SUM(CASE WHEN b.id = " . $b['id'] . " AND bf.name = 'Total Orders' THEN crfv.value ELSE 0 END) as '" . $b['name'] . "'");
        }, $businesses->toArray());
        $businessBonus = array_map(function ($b) {
            return DB::raw("SUM(CASE WHEN b.id = " . $b['id'] . " AND bf.name = 'Bonus' THEN crfv.value ELSE 0 END) as '" . $b['name'] . "_bonus'");
        }, $businesses->toArray());
        $businessTips = array_map(function ($b) {
            return DB::raw("SUM(CASE WHEN b.id = " . $b['id'] . " AND bf.name = 'Tip' THEN crfv.value ELSE 0 END) as '" . $b['name'] . "_tip'");
        }, $businesses->toArray());
        $businessOtherTips = array_map(function ($b) {
            return DB::raw("SUM(CASE WHEN b.id = " . $b['id'] . " AND bf.name = 'Other Tip' THEN crfv.value ELSE 0 END) as '" . $b['name'] . "_other_tip'");
        }, $businesses->toArray());


        $payrollReport = $model->newQuery()
            ->select(array_merge([
                DB::raw('d.name'),
                DB::raw('dt.name as contract_type'),
                DB::raw('d.iqaama_number'),
                DB::raw('d.stc_pay'),
                DB::raw('d.bank_name'),
                DB::raw('d.iban'),
                DB::raw('c.name as nationality'),
                DB::raw('COUNT(d.id) AS driver_id'),
                DB::raw('COUNT(coordinator_reports.id) AS working_days'),
                DB::raw('SUM(CASE WHEN bf.name = "Total Orders" THEN crfv.value ELSE 0 END) AS total_orders'),
                DB::raw('SUM(CASE WHEN bf.name = "Bonus" THEN crfv.value ELSE 0 END) AS total_bonus'),
                DB::raw('SUM(CASE WHEN bf.name = "Tip" THEN crfv.value ELSE 0 END) AS total_tip_amount'),
                DB::raw('SUM(CASE WHEN bf.name = "Other Tip" THEN crfv.value ELSE 0 END) AS total_other_tip_amount'),
                DB::raw('
                    ROUND(CASE 
                        WHEN 
                            dt.name = "FREELANCER" 
                        THEN 
                            (' . $BASE_SALARY_PER_MONTH / $WORKING_DAYS_PER_MONTH . ') * LEAST(COUNT(coordinator_reports.id), ' . $WORKING_DAYS_PER_MONTH . ')
                        ELSE
                            ' . $BASE_SALARY_PER_MONTH . ' 
                    END, 2) as base_salary
                '),
                    DB::raw('
                    CASE 
                        WHEN 
                            dt.name = "FREELANCER" 
                        THEN 
                            (' . $BASE_ORDER_LIMIT_PER_MONTH / $WORKING_DAYS_PER_MONTH . ') * LEAST(COUNT(coordinator_reports.id), ' . $WORKING_DAYS_PER_MONTH . ')
                        ELSE
                            ' . $BASE_ORDER_LIMIT_PER_MONTH . ' 
                    END as base_order_limit
                '),
            ], $businessOrders, $businessBonus, $businessTips, $businessOtherTips)) 
            ->leftJoin('drivers AS d', 'd.id', '=', 'coordinator_reports.driver_id')
            ->leftJoin('driver_types AS dt', 'd.driver_type_id', '=', 'dt.id')
            ->leftJoin('countries AS c', 'd.nationality_id', '=', 'c.id')
            ->leftJoin('businesses AS b', 'b.id', '=', 'coordinator_reports.business_id')
            ->leftJoin('coordinator_report_field_values AS crfv', 'crfv.coordinator_report_id', '=', 'coordinator_reports.id')
            ->leftJoin('business_fields AS bf', 'bf.id', '=', 'crfv.field_id')
            ->when($request->startDate, fn ($q) => $q->whereDate('coordinator_reports.created_at', '>=', $request->startDate))
            ->when($request->endDate, fn ($q) => $q->whereDate('coordinator_reports.created_at', '<=', $request->endDate))
            ->groupBy('coordinator_reports.driver_id');

        return $model->newQuery()->from(DB::raw("({$payrollReport->toSql()}) as payroll_report"))
                ->select([ 
                    'payroll_report.*',
                    DB::raw('
                        CASE 
                            WHEN 
                                payroll_report.total_orders > payroll_report.base_order_limit
                            THEN
                                (payroll_report.total_orders - payroll_report.base_order_limit) * ' . $COMMISSION_RATE . '
                            ELSE
                                0
                        END as commission_amount
                    '),
                    DB::raw('
                        CASE 
                            WHEN 
                                payroll_report.total_orders <= payroll_report.base_order_limit
                            THEN
                                (payroll_report.base_salary / payroll_report.base_order_limit) * (payroll_report.base_order_limit - payroll_report.total_orders)
                            ELSE
                                0
                        END as deductions
                    '),
                    DB::raw('
                        ROUND((base_salary + (
                            payroll_report.total_bonus + payroll_report.total_tip_amount + payroll_report.total_other_tip_amount
                        ) + (
                            CASE 
                                WHEN 
                                    payroll_report.total_orders > payroll_report.base_order_limit
                                THEN
                                    (payroll_report.total_orders - payroll_report.base_order_limit) * ' . $COMMISSION_RATE . '
                                ELSE
                                    0
                            END
                        )) - (
                            CASE 
                                WHEN 
                                    payroll_report.total_orders <= payroll_report.base_order_limit
                                THEN
                                    (payroll_report.base_salary / payroll_report.base_order_limit) * (payroll_report.base_order_limit - payroll_report.total_orders)
                                ELSE
                                    0
                            END
                        ), 2) as salary
                    ')
                ])
                ->mergeBindings($payrollReport->getQuery());
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('drivers-payroll-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('create'),
                Button::make('export'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $businesses = Business::select('id', 'name')->get();
        $columns = [];

        $columns = array_merge($columns, [
            Column::make('iqaama_number'),
            Column::make('name'),
            // Column::make('nationality'),
            Column::make('contract_type'),
            Column::make('working_days'),
        ]);

        // $columns = array_merge($columns, array_map(fn ($b) => Column::make($b['name']), $businesses->toArray()));
        // $columns = array_merge($columns, array_map(fn ($b) => Column::make($b['name'] . '_bonus'), $businesses->toArray()));
        // $columns = array_merge($columns, array_map(fn ($b) => Column::make($b['name'] . '_tip'), $businesses->toArray()));
        // $columns = array_merge($columns, array_map(fn ($b) => Column::make($b['name'] . '_other_tip'), $businesses->toArray()));

        $columns = array_merge($columns, [
            Column::make('total_orders'),
            Column::make('deductions'),
            Column::make('commission_amount'),
            Column::make('base_salary'),
            Column::make('salary'),
            // Column::make('stc_pay'),
            // Column::make('bank_name'),
            // Column::make('iban'),
        ]);
        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Drivers_' . date('YmdHis');
    }
}
