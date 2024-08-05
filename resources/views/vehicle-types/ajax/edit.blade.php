@php
$addDesignationPermission = user()->permission('add_designation');
@endphp

<link rel="stylesheet" href="{{ asset('vendor/css/tagify.css') }}">

<div class="row">
    <div class="col-sm-12">
        <x-form id="update-vehicle-data-form" method="PUT">

            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('modules.vehicleTypes.personalDetails')
                </h4>

                <div class="col-lg-3">
                    <x-forms.file allowedFileExtensions="png jpg jpeg svg bmp" class="mr-0 mr-lg-2 mr-md-2 cropper"
                        :fieldLabel="__('modules.make_model.logoImage')" fieldName="image" fieldId="image" :fieldValue="asset('user-uploads/vehicle-type/' . $vehicle_type->image)"
                        fieldHeight="119" :popover="__('messages.fileFormat.ImageFile')" />
                </div>

                <div class="row  p-20">
                    <div class="col-md-4">
                        <x-forms.text fieldId="name" :fieldLabel="__('modules.vehicles.name')"
                            fieldName="name" fieldRequired="true" :fieldValue="$vehicle_type->name"
                            :fieldPlaceholder="__('modules.vehicles.nameInfo')">
                        </x-forms.text>
                    </div>
                </div>


                <x-form-actions>
                    <x-forms.button-primary id="save-vehicle-form" class="mr-3" icon="check">
                        @lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-secondary class="mr-3" id="save-more-vehicle-form" icon="check-double">@lang('app.saveAddMore')
                    </x-forms.button-secondary>
                    <x-forms.button-cancel class="border-0 " data-dismiss="modal">@lang('app.cancel')
                    </x-forms.button-cancel>

                </x-form-actions>
            </div>
        </x-form>

    </div>
</div>

<script src="{{ asset('vendor/jquery/tagify.min.js') }}"></script>
@if (function_exists('sms_setting') && sms_setting()->telegram_status)
    <script src="{{ asset('vendor/jquery/clipboard.min.js') }}"></script>
@endif
<script>
    $(document).ready(function() {
        const route = "{{ route('vehicle-types.update', ':id') }}";
        const url = route.replace(':id', "{{ $vehicle_type->id }}");

        $('#save-more-vehicle-form').click(function() {
            $('#add_more').val(true);
            var data = $('#update-vehicle-data-form').serialize();
            saveDriver(data, url, "#save-more-vehicle-form");
        });

        $('#save-vehicle-form').click(function() {
            var data = $('#update-vehicle-data-form').serialize();
            saveDriver(data, url, "#save-vehicle-form");
        });

        function saveDriver(data, url, buttonSelector) {
            $.easyAjax({
                url: url,
                container: '#update-vehicle-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: buttonSelector,
                file: true,
                data: data,
                success: function(response) {
                    if (response.status == 'success') {
                        if ($(MODAL_XL).hasClass('show')) {
                            $(MODAL_XL).modal('hide');
                            window.location.reload();
                        }
                        else if(response.add_more == true) {

                            var right_modal_content = $.trim($(RIGHT_MODAL_CONTENT).html());

                            if(right_modal_content.length) {

                                $(RIGHT_MODAL_CONTENT).html(response.html.html);
                                $('#add_more').val(false);
                            }
                            else {

                                $('.content-wrapper').html(response.html.html);
                                init('.content-wrapper');
                                $('#add_more').val(false);
                            }

                        }
                        else {

                            window.location.href = response.redirectUrl;

                        }

                        if (typeof showTable !== 'undefined' && typeof showTable === 'function') {
                            showTable();
                        }

                    }

                }
            });
        }

        $('#country').change(function(){
            var phonecode = $(this).find(':selected').data('phonecode');
            $('#country_phonecode').val(phonecode);
            $('.select-picker').selectpicker('refresh');
        });


        init(RIGHT_MODAL);
    });

    $('.cropper').on('dropify.fileReady', function(e) {
        var inputId = $(this).find('input').attr('id');
        var url = "{{ route('cropper', ':element') }}";
        url = url.replace(':element', inputId);
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    })
</script>
