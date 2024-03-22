@php
$addDesignationPermission = user()->permission('add_designation');
@endphp

<link rel="stylesheet" href="{{ asset('vendor/css/tagify.css') }}">

<div class="row">
    <div class="col-sm-12">
        <x-form id="save-driver-project-data-form">

            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('modules.businesses.projectDetails')
                </h4>

                <div class="row  p-20">
                    <div class="col-md-4">
                        <x-forms.text fieldId="name" :fieldLabel="__('modules.businesses.name')"
                            fieldName="name" fieldRequired="true"
                            :fieldPlaceholder="__('modules.businesses.name')">
                        </x-forms.text>
                    </div>
                </div>

                <x-forms.custom-field :fields="$fields"></x-forms.custom-field>

                <x-form-actions>
                    <x-forms.button-primary id="save-driver-project-form" class="mr-3" icon="check">
                        @lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-secondary class="mr-3" id="save-more-driver-project-form" icon="check-double">@lang('app.saveAddMore')
                    </x-forms.button-secondary>
                    <x-forms.button-cancel class="border-0 " data-dismiss="modal">@lang('app.cancel')
                    </x-forms.button-cancel>

                </x-form-actions>
            </div>
        </x-form>

    </div>
</div>

<script>
    $(document).ready(function() {
        $('#save-more-driver-project-form').click(function() {

            $('#add_more').val(true);

            const url = "{{ route('drivers.store') }}";
            var data = $('#save-driver-project-data-form').serialize();
            saveDriverProject(data, url, "#save-more-driver-project-form");


        });

        $('#save-driver-project-form').click(function() {

            const url = "{{ route('businesses.store') }}";
            var data = $('#save-driver-project-data-form').serialize();
            saveDriverProject(data, url, "#save-driver-project-form");

        });

        function saveDriverProject(data, url, buttonSelector) {
            $.easyAjax({
                url: url,
                container: '#save-driver-project-data-form',
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

        init(RIGHT_MODAL);
    });
</script>
