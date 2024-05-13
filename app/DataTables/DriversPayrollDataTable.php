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
        return (new EloquentDataTable($query));
            // ->addColumn('name', 'drivers.datatable.name-with-image')
            // ->addColumn('action', 'drivers.datatable.action')
            // ->addColumn('status', 'drivers.datatable.status')
            // ->setRowId('id')
            // ->rawColumns([ 'name', 'status', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(CoordinatorReport $model): QueryBuilder
    {
        $businesses = Business::select('id', 'name')->get();
        $businessOrders = array_map(function ($b) {
            return DB::raw("SUM(CASE WHEN b.id = " . $b['id'] . " THEN crfv.value ELSE 0 END) as '" . $b['name'] . "'");
        }, $businesses->toArray());

        return $model->newQuery()
        
            ->select(array_merge([ 
                'coordinator_reports.driver_id', 
                'd.name', 
                'd.iqaama_number', 
                DB::raw('SUM(crfv.value) AS total_orders'),
                DB::raw('CASE 
                            WHEN SUM(crfv.value) > 250 
                                THEN SUM(crfv.value) * 9
                            ELSE 0 
                        END AS commission_amount'),
                DB::raw('ROUND(CASE 
                            WHEN SUM(crfv.value) > 250 
                                THEN 400 + (SUM(crfv.value) * 9) 
                            ELSE ((SUM(crfv.value) * 1.6) - ((250 - SUM(crfv.value)) * 1.6)) 
                        END, 2) AS salary'),
            ], $businessOrders))
            ->leftJoin('drivers AS d', 'd.id', '=', 'coordinator_reports.driver_id')
            ->leftJoin('businesses AS b', 'b.id', '=', 'coordinator_reports.business_id')
            ->leftJoin('coordinator_report_field_values AS crfv', 'crfv.coordinator_report_id', '=', 'coordinator_reports.id')
            ->leftJoin('business_fields AS bf', 'bf.id', '=', 'crfv.field_id')
            ->where('bf.name', '=', 'Total Orders')
            ->groupBy('coordinator_reports.driver_id');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('drivers-table')
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
            Column::make('driver_id'),
            Column::make('name'),
            Column::make('iqaama_number'),
        ]);

        $columns = array_merge($columns, array_map(fn ($b) => Column::make($b['name']), $businesses->toArray()));

        $columns = array_merge($columns, [ 
            Column::make('commission_amount'),
            Column::make('total_orders'),
            Column::make('salary'),
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
