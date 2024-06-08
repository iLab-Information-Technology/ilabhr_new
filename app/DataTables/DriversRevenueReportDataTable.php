<?php

namespace App\DataTables;

use App\Models\Driver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DriversRevenueReportDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('branch', function ($row) {
                return $row->branch->name ?? '';
            })
            ->addColumn('contract', function ($row) {
                return $row->driver_type->name ?? '';
            })
            ->addColumn('total_orders', function ($row) {
                $totalSum = $row->coordinator_reports->sum(function ($report) {
                    return $report->field_values->sum('value');
                });
                return number_format($totalSum);
            })
            ->addColumn('total_cost', function ($row) {
                $totalSum = $row->coordinator_reports->sum(function ($report) {
                    return $report->field_values->sum('value');
                });
                $total_salary = $this->calculate_driver_order_price($totalSum, 26, true);
                $total_coordinate_days = $row->coordinator_reports->count();
                $total_gprs = $row->gprs / $total_coordinate_days;
                $total_fuel = $row->fuel / $total_coordinate_days;
                $total_government_cost = $row->government_cost / $total_coordinate_days;
                $total_accommodation = $row->accommodation / $total_coordinate_days;
                $total_vehicle_monthly_cost = $row->vehicle_monthly_cost / $total_coordinate_days;
                $total_mobile_data = $row->mobile_data / $total_coordinate_days;

                return number_format($total_salary + $total_gprs + $total_fuel + $total_government_cost + $total_accommodation + $total_vehicle_monthly_cost + $total_mobile_data);
            })
            ->addColumn('profit_loss', function ($row) {
                $totalSum = $row->coordinator_reports->sum(function ($report) {
                    return $report->field_values->sum('value');
                });
                $total_salary = $this->calculate_driver_order_price($totalSum, 26, true);
                $total_coordinate_days = $row->coordinator_reports->count();
                $total_gprs = $row->gprs / $total_coordinate_days;
                $total_fuel = $row->fuel / $total_coordinate_days;
                $total_government_cost = $row->government_cost / $total_coordinate_days;
                $total_accommodation = $row->accommodation / $total_coordinate_days;
                $total_vehicle_monthly_cost = $row->vehicle_monthly_cost / $total_coordinate_days;
                $total_mobile_data = $row->mobile_data / $total_coordinate_days;

                $total_cost = $total_salary + $total_gprs + $total_fuel + $total_government_cost + $total_accommodation + $total_vehicle_monthly_cost + $total_mobile_data;

                // Placeholder for revenue calculation
                $revenue = 50000; // Replace with actual revenue calculation if needed
                return number_format($revenue - $total_cost);
            });
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Driver $model): QueryBuilder
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        return $model->with([
            'branch',
            'driver_type',
            'coordinator_reports' => function ($query) use ($currentMonth, $currentYear) {
                $query
                    // ->whereMonth('report_date', $currentMonth)
                    // ->whereYear('report_date', $currentYear)
                    ->with(['field_values' => function ($query) {
                          $query->where('field_id', 1);
                    }]);
            }
        ])->select([
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
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('drivers-revenue-report-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
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
        return [
            Column::make('id'),
            Column::make('name'),
            Column::make('branch'),
            Column::make('contract'),
            Column::make('total_orders'),
            Column::make('total_cost'),
            Column::make('profit_loss')->title('Profit/Loss'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'DriversRevenueReport_' . date('YmdHis');
    }

    /**
     * Calculate the driver's order price.
     *
     * @param float $totalOrders
     * @param int $days
     * @param bool $someFlag
     * @return float
     */
    protected function calculate_driver_order_price($totalOrders, $days, $someFlag)
    {
        // Implement your calculation logic here
        // For example:
        return $totalOrders * 10; // Replace with actual calculation logic
    }
}
