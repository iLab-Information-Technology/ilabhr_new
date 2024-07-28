<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">

<div class="modal-header">
    <h5 class="modal-title">@lang('app.menu.addInsidePicture')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-form id="save-other-report-data-form" method="PUT" class="ajax-form" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-12">
                    <x-forms.file allowedFileExtensions="png jpg jpeg svg pdf doc docx" class="mr-0 mr-lg-2 mr-md-2" :fieldValue="asset('user-uploads/vehicle-images/'. $vehicle->images[0]->firstWhere('type', 'inside')->image)"
                        :fieldLabel="__('modules.vehicles.insidePictures')" fieldName="inside_image" fieldId="file" aria-multiline="true" multiple>
                    </x-forms.file>
                </div>
            </div>
        </x-form>
    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-other-report-form" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    $('#save-other-report-form').click(function() {
        $.easyAjax({
            url: "{{ route('vehicles.update', $vehicle->id) }}",
            container: '#save-other-report-data-form',
            type: "POST",
            disableButton: true,
            blockUI: true,
            buttonSelector: 'save-other-report-form',
            file: true,
            data: $('#save-other-report-data-form').serialize(),
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
