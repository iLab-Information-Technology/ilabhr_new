<link rel="stylesheet" href="{{ asset('vendor/css/tagify.css') }}">

<div class="row">
    <div class="col-sm-12">
        <x-form id="save-vehicle-data-form">
            @method('put')
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('modules.vehicles.personalDetails')
                </h4>

                <div class="col-lg-3">
                    <x-forms.datepicker fieldId="date" :fieldLabel="__('modules.vehicles.date')" fieldName="date" fieldRequired="true" :fieldValue="$vehicle->date ? \Carbon\Carbon::createFromFormat('d-m-Y', $vehicle->date)->format('d-m-Y') : ''"
                        :fieldPlaceholder="__('modules.vehicles.date')" />
                </div>

                <div class="row p-20">
                    <div class="col-md-4">
                        <x-forms.text fieldId="ilab_id" :fieldLabel="__('modules.vehicles.ilab_id')" fieldName="ilab_id" fieldRequired="true" :fieldValue="$vehicle->ilab_id ? $vehicle->ilab_id : ''"
                            :fieldPlaceholder="__('modules.vehicles.ilab_id')">
                        </x-forms.text>
                    </div>

                </div>


                <div class="row  p-20">
                    <div class="col-md-4">
                        <x-forms.select fieldId="vehicle_type_id" :fieldLabel="__('modules.vehicles.vehicleType_id')" fieldName="vehicle_type_id"
                            fieldRequired="true" :Placeholder="__('modules.vehicles.vehicleType_id')">
                            @foreach ($vehicleType as $item)
                                <option value="{{ $item->id }}" {{ $vehicle->vehicle_type_id == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-md-4">
                        <x-forms.text fieldId="vehicle_plate_number" :fieldLabel="__('modules.vehicles.number_plate')" fieldName="vehicle_plate_number" :fieldValue="$vehicle->vehicle_plate_number"
                            fieldRequired="true" :fieldPlaceholder="__('modules.vehicles.number_plate')">
                        </x-forms.text>
                    </div>

                    <div class="col-md-4">
                        <x-forms.select fieldId="make_model_id" :fieldLabel="__('modules.vehicles.makeModel')" fieldName="make_model_id"
                            fieldRequired="true" :Placeholder="__('modules.vehicles.searchMakeModel')">
                            @foreach ($makeModel as $item)
                                <option value="{{ $item->id }}" {{ $vehicle->make_model_id == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-md-4">
                        <x-forms.select fieldId="rental_company_id" :fieldLabel="__('modules.vehicles.rentalCompany')" fieldName="rental_company_id"
                            fieldRequired="true" :Placeholder="__('modules.vehicles.searchRentalCompany')">
                            @foreach ($rentalCompany as $item)
                                <option value="{{ $item->id }}" {{ $vehicle->rental_company_id == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-md-4">
                        <x-forms.select fieldId="color" :fieldLabel="__('modules.vehicles.color')" fieldName="color" fieldRequired="true" :fieldValue="$vehicle->color"
                            :Placeholder="__('modules.vehicles.color')">
                            <option value="white" {{ $vehicle->color == 'white' ? 'selected' : '' }}>White</option>
                            <option value="gray" {{ $vehicle->color == 'gray' ? 'selected' : '' }}>Gray</option>
                            <option value="silver" {{ $vehicle->color == 'silver' ? 'selected' : '' }}>Silver</option>
                            <option value="brown" {{ $vehicle->color == 'brown' ? 'selected' : '' }}>Brown</option>
                            <option value="black" {{ $vehicle->color == 'black' ? 'selected' : '' }}>Black</option>
                        </x-forms.select>
                    </div>

                    <div class="col-md-4">
                        <x-forms.select fieldId="vehicleStatus" :fieldLabel="__('modules.vehicles.status')" fieldName="status" fieldRequired="true"
                            :Placeholder="__('modules.vehicles.status')">
                            <option value="0" {{ $vehicle->status == 0 ? 'selected' : '' }}>Active</option>
                            <option value="1" {{ $vehicle->status == 1 ? 'selected' : '' }}>Inactive</option>
                            <option value="3" {{ $vehicle->status == 3 ? 'selected' : '' }}>Replacement</option>
                        </x-forms.select>
                    </div>


                </div>
                <div class="row p-20 {{ $vehicle->status == 3 ? '' : 'd-none' }}" id="replacementRow">


                    {{-- Start: Replacement Date --}}
                    <div class="col-md-4">
                        <x-forms.datepicker fieldId="replacement_date" :fieldLabel="__('modules.vehicles.replacementDate')" fieldName="replacement_date"
                            :fieldPlaceholder="__('placeholders.date')" />
                    </div>
                    {{-- End: Replacement Date --}}

                    {{-- Start: Replacement Reason --}}
                    <div class="col-md-4">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.vehicles.replacementReason')"
                                fieldName="replacement_reason" fieldId="replacement_reason" :fieldPlaceholder="__('modules.vehicles.replacementReason')">
                            </x-forms.textarea>
                        </div>
                    </div>
                    {{-- End: Replacement Reason --}}

                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-vehicle-form" class="mr-3" icon="check">
                        @lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-secondary class="mr-3" id="save-more-vehicle-form"
                        icon="check-double">@lang('app.saveAddMore')
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

        $('#save-more-vehicle-form').click(function() {

            $('#add_more').val(true);

            const url = "{{ route('vehicles.update', $vehicle->id) }}";
            var data = $('#save-vehicle-data-form').serialize();
            saveDriver(data, url, "#save-more-vehicle-form");


        });

        $('#save-vehicle-form').click(function() {
            const url = "{{ route('vehicles.update', $vehicle->id) }}";
            var data = $('#save-vehicle-data-form').serialize();
            saveDriver(data, url, "#save-vehicle-form");

        });

        function saveDriver(data, url, buttonSelector) {
            $.easyAjax({
                url: url,
                container: '#save-vehicle-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: buttonSelector,
                file: true,
                data: data,
                success: function(response) {
                    console.log(response);
                    if (response.status == 'success') {
                        if ($(MODAL_XL).hasClass('show')) {
                            $(MODAL_XL).modal('hide');
                            window.location.reload();
                        } else if (response.add_more == true) {

                            var right_modal_content = $.trim($(RIGHT_MODAL_CONTENT).html());

                            if (right_modal_content.length) {

                                $(RIGHT_MODAL_CONTENT).html(response.html.html);
                                $('#add_more').val(false);
                            } else {

                                $('.content-wrapper').html(response.html.html);
                                init('.content-wrapper');
                                $('#add_more').val(false);
                            }

                        } else {

                            window.location.href = response.redirectUrl;

                        }

                        if (typeof showTable !== 'undefined' && typeof showTable === 'function') {
                            showTable();
                        }

                    }

                }
            });
        }

        datepicker('#replacement_date', {
            position: 'bl',
            maxDate: new Date(),
            ...datepickerConfig
        });

        datepicker('#date', {
            position: 'bl',
            ...datepickerConfig
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

    $("#vehicleStatus").on('change', function() {
        const status = $(this).val();
        if (status == 3) {
            $("#replacementRow").removeClass('d-none')
        } else {
            $("#replacementRow").addClass('d-none')
        }
    })
</script>
