<link rel="stylesheet" href="{{ asset('vendor/css/tagify.css') }}">

<div class="row">
    <div class="col-sm-12">
        <x-form id="save-driver-data-form">

            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('modules.vehicles.personalDetails')
                </h4>

                <div class="col-lg-3">
                    <x-forms.datepicker fieldId="date" :fieldLabel="__('modules.vehicles.date')"
                            fieldName="date" fieldRequired="true" :fieldPlaceholder="__('modules.vehicles.date')" />
                </div>

                <div class="col-md-4">
                    <x-forms.text fieldId="ilab_id" :fieldLabel="__('modules.vehicles.ilab_id')"
                        fieldName="ilab_id" fieldRequired="true"
                        :fieldPlaceholder="__('modules.vehicles.ilab_id')">
                    </x-forms.text>
                </div>

                <div class="row  p-20">
                    <div class="col-md-4">
                        <x-forms.select fieldId="vehicle_type_id" :fieldLabel="__('modules.vehicles.vehicleType_id')"
                            fieldName="vehicle_type_id" fieldRequired="true"
                            :fieldPlaceholder="__('modules.vehicles.vehicleType_id')">
                            @foreach ($vehicleType as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-md-4">
                        <x-forms.text fieldId="number_plate" :fieldLabel="__('modules.vehicles.number_plate')"
                            fieldName="ilab_id" fieldRequired="true"
                              :fieldPlaceholder="__('modules.vehicles.number_plate')">
                        </x-forms.text>
                    </div>

                    <div class="col-md-4">
                        <x-forms.select fieldId="driver_id" :fieldLabel="__('modules.vehicles.driver')"
                            fieldName="driver_type_id" fieldRequired="true"
                            :fieldPlaceholder="__('modules.vehicles.searchDriver')">
                            @foreach ($drivers as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-md-4">
                        <x-forms.select fieldId="make_model_id" :fieldLabel="__('modules.vehicles.makeModel')"
                            fieldName="make_model_id" fieldRequired="true"
                            :fieldPlaceholder="__('modules.vehicles.searchMakeModel')">
                            @foreach ($makeModel as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-md-4">
                        <x-forms.select fieldId="rental_company_id" :fieldLabel="__('modules.vehicles.rentalCompany')"
                            fieldName="rental_company_id" fieldRequired="true"
                            :fieldPlaceholder="__('modules.vehicles.searchRentalCompany')">
                            @foreach ($rentalCompany as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                </div>

                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-top-grey">
                    @lang('modules.drivers.contact')
                </h4>

                <div class="row p-20">


                    <div class="col-md-6">



                    </div>

                </div>
                <x-form-actions>
                    <x-forms.button-primary id="save-driver-form" class="mr-3" icon="check">
                        @lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-secondary class="mr-3" id="save-more-driver-form" icon="check-double">@lang('app.saveAddMore')
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

        $('#driver_type_id').change(function(){
            changeInputFields($(this).val());
        });

        changeInputFields($('#driver_type_id').val());
        function changeInputFields(driver_type_id){
            $.easyAjax({
                url: "{{ route('drivers.get-driver-type') }}",
                type: "GET",
                data: {id: driver_type_id},
                success: function(response) {

                    const fields = response.fields.split(',');
                     ['vehicle_monthly_cost', 'mobile_data', 'accommodation', 'government_cost'].forEach(field => $(`.${field}_div`).addClass('d-none'));
                    fields.forEach(field => $(`.${field}_div`).removeClass('d-none'));
                }
            });
        }

        datepicker('#date_of_birth', {
            position: 'bl',
            maxDate: new Date(),
            ...datepickerConfig
        });

        datepicker('#iqaama_expiry_date', {
            position: 'bl',
            ...datepickerConfig
        });

        datepicker('#license_expiry_date', {
            position: 'bl',
            ...datepickerConfig
        });

        datepicker('#insurance_expiry_date', {
            position: 'bl',
            ...datepickerConfig
        });

        $('#save-more-driver-form').click(function() {

            $('#add_more').val(true);

            const url = "{{ route('drivers.store') }}";
            var data = $('#save-driver-data-form').serialize();
            saveDriver(data, url, "#save-more-driver-form");


        });

        $('#save-driver-form').click(function() {

            const url = "{{ route('drivers.store') }}";
            var data = $('#save-driver-data-form').serialize();
            saveDriver(data, url, "#save-driver-form");

        });

        function saveDriver(data, url, buttonSelector) {
            $.easyAjax({
                url: url,
                container: '#save-driver-data-form',
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
