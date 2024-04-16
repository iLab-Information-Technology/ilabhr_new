@php
$deleteLinkedDriverPermission = $employee->permission('delete_linked_drivers');
@endphp

<div class="task_view">

    <div class="dropdown">
        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
            id="dropdownMenuLink-{{ $id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="icon-options-vertical icons"></i>
        </a>
        @if ($deleteLinkedDriverPermission == 'all')
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-{{ $id }}"
            tabindex="0">
            <a class="dropdown-item delete-table-row" href="javascript:;" data-driver-business-id="{{ $driver_id }}">
                <i class="fa fa-trash mr-2"></i>
                {{ trans('app.delete') }}
            </a>
        </div>
        @endif
    </div>
</div>
