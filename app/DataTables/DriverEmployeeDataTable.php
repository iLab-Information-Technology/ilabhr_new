<?php

namespace App\DataTables;

use App\Models\DriverEmployee;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DriverEmployeeDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', fn ($driverEmployee) => view('employees.drivers.datatable.action', array_merge($driverEmployee->toArray(), [ 'employee' => $this->employee])))
            ->setRowId('id')
            ->rawColumns([ 'action' ]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(DriverEmployee $model): QueryBuilder
    {
        return $model
            ->newQuery()
            ->with(['driver'])
            ->select('driver_employee.*')
            ->when($this->employee_id, fn ($q) => $q->where('employee_id', $this->employee_id));
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('driver_employee-table')
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
        return [
            Column::make('id'),
            Column::make('name')
                ->name('driver.name')
                ->data('driver.name'),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Drivers_' . date('YmdHis');
    }
}
