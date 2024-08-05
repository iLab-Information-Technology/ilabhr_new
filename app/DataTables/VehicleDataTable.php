<?php

namespace App\DataTables;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class VehicleDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('vehicle_plate_number', function ($vehicle) {
                // Create a link to the vehicle's show route
                return view('vehicles.datatable.number-plate-link', get_defined_vars());
            })
            ->addColumn('make_model', function($vehicle){
                return view('vehicles.datatable.name-with-image', [
                    'path' => 'make-model',
                    'name' => $vehicle->makeModel->name,
                    'image' => $vehicle->makeModel->image,
                ]);
            })
            ->addColumn('rental_company', function($vehicle){
                return view('vehicles.datatable.name-with-image', [
                    'path' => 'rental-company',
                    'name' => $vehicle->rentalCompany->name,
                    'image' => $vehicle->rentalCompany->image,
                ]);
            })
            ->addColumn('driver_name', function ($vehicle) {
                return $vehicle->driver ? $vehicle->driver->name : '---'; // Return the driver's name if the driver exists
            })
            ->addColumn('iqaama_number', function ($vehicle) {
                return $vehicle->driver ? $vehicle->driver->iqaama_number : '---'; // Return the driver's iqaama number if the driver exists
            })
            ->addColumn('status', function ($vehicle) {
                // Map the status value to human-readable text
                switch ($vehicle->status) {
                    case 0:
                        return 'Active';
                    case 1:
                        return 'Inactive';
                    case 3:
                        return 'Replacement';
                    default:
                        return 'Unknown';
                }
            })
            ->addColumn('action', 'vehicles.datatable.action')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Vehicle $model): QueryBuilder
    {
        return $model->newQuery()->with('driver', 'rentalCompany', 'vehicleType', 'makeModel');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('vehicle-table')
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
            Column::make('date'),
            Column::make('vehicle_plate_number'),
            Column::make('make_model'),
            Column::make('rental_company'),
            Column::make('driver_name'),
            Column::make('iqaama_number'),
            Column::make('status'),
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
        return 'Vehicle_' . date('YmdHis');
    }
}
