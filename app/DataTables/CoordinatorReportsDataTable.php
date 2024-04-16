<?php

namespace App\DataTables;

use App\Models\Business;
use App\Models\CoordinatorReport;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CoordinatorReportsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $business = Business::find($this->business_id);
        
        $dataTable = (new EloquentDataTable($query));
        $rawColumns = [ 'action' ];
        $rawColumns = array_merge(['action'], array_map(fn ($f) => $f['name'], $business->fields->toArray()));

        foreach ($business->fields as $field) {
            $dataTable->addColumn($field['name'], function ($report) use ($field) {
                $fieldValue = $report->field_values()->where('field_id', $field['id'])->first();

                if ($field->type == 'DOCUMENT' && $fieldValue) {
                    $url = asset_url_local_s3("coordinator-reports/" . $fieldValue->value);
                    return '<img src="' . $url . '" class="hw-50px object-fit-cover" />';
                }

                return $fieldValue ? $fieldValue->value : '-';
            });
        }

        $dataTable->addColumn('created_at', fn ($report) => $report->created_at->format('d/m/Y'));
        $dataTable->addColumn('action', 'coordinator-report.datatable.action')
            ->setRowId('id')
            ->rawColumns($rawColumns);

        return $dataTable;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(CoordinatorReport $model): QueryBuilder
    {
        return $model->where('business_id', $this->business_id)->with([ 'driver', 'field_values'])->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('coordinator-report-table')
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
        $business = Business::find($this->business_id);
        $businessFields = array_map(function ($field) {
            return Column::make($field['name'])->orderable(false)->searchable(false);
        }, $business->fields->toArray());

        return array_merge([
            Column::make('driver.name')
        ], $businessFields, [
            Column::make('created_at'),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center'),
        ]);
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'CoordinatorReports_' . date('YmdHis');
    }
}
