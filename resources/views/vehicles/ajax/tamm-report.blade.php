<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">

<div class="modal-header">
    <h5 class="modal-title">@lang('app.menu.addTammReport')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-form id="save-tamm-report-data-form" method="PUT" class="ajax-form" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-3">
                    <x-forms.datepicker 
                        fieldId="tamm_expiry_date" 
                        :fieldLabel="__('modules.drivers.expiryDate')" 
                        :fieldValue="$vehicle->istimarah_expiry_date ? \Carbon\Carbon::createFromFormat('Y-m-d', $vehicle->istimarah_expiry_date)->format('d-m-Y') : ''"
                        fieldName="tamm_expiry_date" 
                        :fieldPlaceholder="__('placeholders.date')" 
                    />
                </div>

                <div class="col-lg-12">
                    <x-forms.file allowedFileExtensions="png jpg jpeg svg pdf doc docx" class="mr-0 mr-lg-2 mr-md-2" :fieldValue="$vehicle->tamm_report ? asset('user-uploads/tamm-report/' . $vehicle->tamm_report) : ''"
                        :fieldLabel="__('modules.vehicles.tammReport')" fieldName="tamm_report" fieldId="file">
                    </x-forms.file>
                </div>
            </div>
        </x-form>
    </div>
</div>

<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-tamm-report-form" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    datepicker('#tamm_expiry_date', {
        position: 'bl',
        ...datepickerConfig
    });

    $('#save-tamm-report-form').click(function() {
        $.easyAjax({
            url: "{{ route('vehicles.update', $vehicle->id) }}",
            container: '#save-tamm-report-data-form',
            type: "POST",
            disableButton: true,
            blockUI: true,
            buttonSelector: 'save-tamm-report-form',
            file: true,
            data: $('#save-tamm-report-data-form').serialize(),
            success: function(response) {
                console.log(response);
                if (response.status === 'success') {
                    window.location.reload();
                }
            }
        });
    });

    init(MODAL_LG);
</script>
