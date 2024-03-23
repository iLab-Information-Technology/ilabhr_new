@php
$addDesignationPermission = user()->permission('add_designation');
@endphp

<link rel="stylesheet" href="{{ asset('vendor/css/tagify.css') }}">

<div class="row">
    <div class="col-sm-12">
        <x-form id="update-business-data-form" method="PUT">

            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('modules.businesses.businessDetails')
                </h4>

                <div class="row  p-20">
                    <div class="col-md-4">
                        <x-forms.text fieldId="name" :fieldLabel="__('modules.businesses.name')"
                            fieldName="name" fieldRequired="true"
                            :fieldPlaceholder="__('modules.businesses.name')" :fieldValue="$business->name">
                        </x-forms.text>
                    </div>
                </div>

                <x-forms.custom-field :fields="$fields"></x-forms.custom-field>

                <x-form-actions>
                    <x-forms.button-primary id="update-business-form" class="mr-3" icon="check">
                        @lang('app.update')
                    </x-forms.button-primary>
                    <x-forms.button-cancel class="border-0 " data-dismiss="modal">@lang('app.cancel')
                    </x-forms.button-cancel>

                </x-form-actions>
            </div>
        </x-form>

    </div>
</div>

<script>
    $(document).ready(function() {

        $('#update-business-form').click(function() {
            const url = "{{ route('businesses.update', $business->id) }}";
            var data = $('#update-business-data-form').serialize();
            updateBusiness(data, url, "#update-business-form");

        });

        function updateBusiness(data, url, buttonSelector) {
            $.easyAjax({
                url: url,
                container: '#update-business-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: buttonSelector,
                file: true,
                data: data,
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = '{{ route('businesses.index') }}';
                    }

                }
            });
        }

        init(RIGHT_MODAL);
    });
</script>
