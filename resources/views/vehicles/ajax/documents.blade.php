<!-- IQAMA ROW START -->
<div class="row">
    <!--  USER CARDS START -->
    <div class="col-xl-12 col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0 mt-5">
        @if(is_null($vehicle->istimarah))
             <x-forms.button-primary class="mr-3 add-document mb-3" icon="plus"  data-tab="istimarah">
                @lang('modules.vehicles.addIstimarah')
            </x-forms.button-primary>
        @endif
        <x-cards.data :title="__('modules.vehicles.istimarahDetails')">

            @if($vehicle->istimarah)
                <x-slot name="action">
                    <div class="dropdown">
                        <button class="btn f-14 px-0 py-0 text-dark-grey dropdown-toggle" type="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-ellipsis-h"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                            aria-labelledby="dropdownMenuLink" tabindex="0">
                                <a class="dropdown-item edit-document"  data-tab="istimarah"
                                    href="javascript:;">@lang('app.edit')</a>
                        </div>

                    </div>
                </x-slot>

                <x-cards.data-row :label="__('modules.drivers.expiryDate')" :value=" $vehicle->istimarah_expiry_date  ? $vehicle->istimarah_expiry_date : '--'" />
                <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block text-capitalize">
                        @lang('modules.employees.scanCopy')</p>
                    <p class="mb-0 text-dark-grey f-14 w-70">
                        @if($vehicle->istimarah)
                            <a target="_blank" class="text-dark-grey"
                                href="{{ asset('user-uploads/istimarah/' . $vehicle->istimarah) }}"><i class="fa fa-external-link-alt"></i> <u>@lang('app.viewScanCopy')</u></a>
                        @else
                        --
                        @endif

                    </p>
                </div>

            @else
                <x-cards.no-record-found-list colspan="5"/>
            @endif
        </x-cards.data>
    </div>
    <!--  USER CARDS END -->
</div>
<!-- IQAMA ROW END -->

<!-- MEDICAL ROW START -->
<div class="row">
    <!--  USER CARDS START -->
    <div class="col-xl-12 col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0 mt-5">
        @if(is_null($vehicle->tamm_report))
             <x-forms.button-primary class="mr-3 add-document mb-3" icon="plus" data-tab="tamm_report">
                @lang('modules.vehicles.addTammReport')
            </x-forms.button-primary>
        @endif
        <x-cards.data :title="__('modules.vehicles.tammDetails')">

            @if($vehicle->tamm_report)
                <x-slot name="action">
                    <div class="dropdown">
                        <button class="btn f-14 px-0 py-0 text-dark-grey dropdown-toggle" type="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-ellipsis-h"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                            aria-labelledby="dropdownMenuLink" tabindex="0">
                                <a class="dropdown-item edit-document"  data-tab="tamm_report"
                                    href="javascript:;">@lang('app.edit')</a>
                        </div>

                    </div>
                </x-slot>
                <x-cards.data-row :label="__('modules.drivers.expiryDate')" :value=" $vehicle->tamm_expiry_date  ? $vehicle->tamm_expiry_date : '--'" />
                <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block text-capitalize">
                        @lang('modules.employees.scanCopy')</p>
                    <p class="mb-0 text-dark-grey f-14 w-70">
                        @if($vehicle->tamm_report)
                            <a target="_blank" class="text-dark-grey"
                                href="{{ asset('user-uploads/tamm-report/' . $vehicle->tamm_report) }}"><i class="fa fa-external-link-alt"></i> <u>@lang('app.viewScanCopy')</u></a>
                        @else
                        --
                        @endif

                    </p>
                </div>

            @else
                <x-cards.no-record-found-list colspan="5"/>
            @endif
        </x-cards.data>
    </div>
    <!--  USER CARDS END -->
</div>
<!-- MEDICAL ROW END -->

<!-- OTHER DOCUMENT ROW START -->
<div class="row">
    <!--  USER CARDS START -->
    <div class="col-xl-12 col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0 mt-5">
        @if(is_null($vehicle->other_report))
             <x-forms.button-primary class="mr-3 add-document mb-3" icon="plus"  data-tab="other-report">
                @lang('modules.vehicles.addOtherReport')
            </x-forms.button-primary>
        @endif
        <x-cards.data :title="__('modules.vehicles.otherReportDetails')">

            @if($vehicle->other_report)
                <x-slot name="action">
                    <div class="dropdown">
                        <button class="btn f-14 px-0 py-0 text-dark-grey dropdown-toggle" type="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-ellipsis-h"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                            aria-labelledby="dropdownMenuLink" tabindex="0">
                                <a class="dropdown-item edit-document"  data-tab="other-document"
                                    href="javascript:;">@lang('app.edit')</a>
                        </div>

                    </div>
                </x-slot>

                <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block text-capitalize">
                        @lang('modules.employees.scanCopy')</p>
                    <p class="mb-0 text-dark-grey f-14 w-70">
                        @if($vehicle->other_report)
                            <a target="_blank" class="text-dark-grey"
                                href="{{ asset('user-uploads/other-report/' . $vehicle->other_report) }}"><i class="fa fa-external-link-alt"></i> <u>@lang('app.viewScanCopy')</u></a>
                        @else
                        --
                        @endif

                    </p>
                </div>

            @else
                <x-cards.no-record-found-list colspan="5"/>
            @endif
        </x-cards.data>
    </div>
    <!--  USER CARDS END -->
</div>
<!-- OTHER DOCUMENT ROW END -->

<!-- Inside Picture ROW START -->
<div class="row">
    <!--  USER CARDS START -->
    <div class="col-xl-12 col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0 mt-5">
        @if(is_null($vehicle->images->firstWhere('type', 'inside')))
             <x-forms.button-primary class="mr-3 add-document mb-3" icon="plus"  data-tab="inside-pictures">
                @lang('modules.vehicles.addInsidePictures')
            </x-forms.button-primary>
        @endif
        <x-cards.data :title="__('modules.vehicles.insidePictures')">

            @if($vehicle->images->firstWhere('type', 'inside'))
                <x-slot name="action">
                    <div class="dropdown">
                        <button class="btn f-14 px-0 py-0 text-dark-grey dropdown-toggle" type="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-ellipsis-h"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                            aria-labelledby="dropdownMenuLink" tabindex="0">
                                <a class="dropdown-item edit-document"  data-tab="inside-pictures"
                                    href="javascript:;">@lang('app.edit')</a>
                        </div>

                    </div>
                </x-slot>

                <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block text-capitalize">
                        @lang('modules.employees.scanCopy')</p>
                    <p class="mb-0 text-dark-grey f-14 w-70">
                        @if($vehicle->images)
                            <a target="_blank" class="text-dark-grey"
                                href="{{ asset('user-uploads/vehicle-images/'. $vehicle->images[0]->firstWhere('type', 'inside')->image) }}"><i class="fa fa-external-link-alt"></i> <u>@lang('app.viewScanCopy')</u></a>
                        @else
                        --
                        @endif

                    </p>
                </div>

            @else
                <x-cards.no-record-found-list colspan="5"/>
            @endif
        </x-cards.data>
    </div>
    <!--  USER CARDS END -->
</div>
<!-- OTHER DOCUMENT ROW END -->
<!-- Inside Picture ROW START -->
<div class="row">
    <!--  USER CARDS START -->
    <div class="col-xl-12 col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0 mt-5">
        @if(is_null($vehicle->images->firstWhere('type', 'outside')))
             <x-forms.button-primary class="mr-3 add-document mb-3" icon="plus"  data-tab="outside-picture">
                @lang('modules.vehicles.addOutsidePictures')
            </x-forms.button-primary>
        @endif
        <x-cards.data :title="__('modules.vehicles.outsidePictures')">

            @if($vehicle->images->firstWhere('type', 'outside'))
                <x-slot name="action">
                    <div class="dropdown">
                        <button class="btn f-14 px-0 py-0 text-dark-grey dropdown-toggle" type="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-ellipsis-h"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                            aria-labelledby="dropdownMenuLink" tabindex="0">
                                <a class="dropdown-item edit-document"  data-tab="outside-picture"
                                    href="javascript:;">@lang('app.edit')</a>
                        </div>

                    </div>
                </x-slot>

                <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block text-capitalize">
                        @lang('modules.employees.scanCopy')</p>
                    <p class="mb-0 text-dark-grey f-14 w-70">
                        @if($vehicle->images->firstWhere('type', 'outside'))
                            <a target="_blank" class="text-dark-grey"
                                href="{{ $vehicle->other_report }}"><i class="fa fa-external-link-alt"></i> <u>@lang('app.viewScanCopy')</u></a>
                        @else
                        --
                        @endif

                    </p>
                </div>

            @else
                <x-cards.no-record-found-list colspan="5"/>
            @endif
        </x-cards.data>
    </div>
    <!--  USER CARDS END -->
</div>
<!-- OTHER DOCUMENT ROW END -->

<script>

    // Iqama Start
    $('.add-document, .edit-document').click(function(){
        event.preventDefault(); // Prevent default action
        const tab = $(this).attr('data-tab');
        var url = `{{ route('vehicles.edit', $vehicle->id) }}?tab=${tab}`;
        $(MODAL_LG + ' ' + MODAL_HEADING).html('Loading...');
        $.ajaxModal(MODAL_LG, url);


        
    });

    $('body').on('click', '.delete-iqama', function () {
        Swal.fire({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.recoverRecord')",
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: "@lang('messages.confirmDelete')",
            cancelButtonText: "@lang('app.cancel')",
            customClass: {
                confirmButton: 'btn btn-primary mr-3',
                cancelButton: 'btn btn-secondary'
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {

                var url = "{{ route('vehicles.update', $vehicle->id) }}";
                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    blockUI: true,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function (response) {
                        if (response.status == "success") {
                            window.location.reload();
                        }
                    }
                });
            }
        });
    });
    // Iqama End

</script>
