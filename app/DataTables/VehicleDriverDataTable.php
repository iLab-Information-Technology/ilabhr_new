<?php

namespace App\DataTables;

use App\Models\Vehicle;
use App\Models\VehicleDriver;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class VehicleDriverDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('driver_name', function ($vehicle) {
                return $vehicle->driver ? $vehicle->driver->name : '';
            })
            ->addColumn('branch_name', function ($vehicle) {
                return $vehicle->driver && $vehicle->driver->branch ? $vehicle->driver->branch->name : '';
            })
            ->addColumn('action', 'vehicles.drivers.datatable.action')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Vehicle $model): QueryBuilder
    {
        return $model->newQuery()->with(['driver', 'driver.branch'])->where('id', $this->vehicle_id)->distinct();
    }

    protected function getQueryParameters()
    {
        return array_merge($this->request->all(), $this->additionalParameters);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('vehicledriver-table')
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
            Column::make('driver_name'),
            Column::make('branch_name'),
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
        return 'VehicleDriver_' . date('YmdHis');
    }
}
