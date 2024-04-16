@php
$addDesignationPermission = user()->permission('add_designation');
@endphp

<link rel="stylesheet" href="{{ asset('vendor/css/tagify.css') }}">

<div class="row">
    <div class="col-sm-12">
        <x-form id="save-business-data-form">

            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('modules.businesses.businessDetails')
                </h4>

                <div class="row  p-20">
                    <div class="col-md-4">
                        <x-forms.text fieldId="name" :fieldLabel="__('modules.businesses.name')"
                            fieldName="name" fieldRequired="true"
                            :fieldPlaceholder="__('modules.businesses.name')">
                        </x-forms.text>
                    </div>
                </div>

                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('modules.businesses.businessFields')
                </h4>

                
                <div id="newFields">

                </div>

                <div class="row p-20" id="add-new-field">
                    <div class="col-md-4">
                        <x-forms.text fieldId="new_field_name" :fieldLabel="__('modules.businesses.name')"
                            fieldName="new_field_name" fieldRequired="false"
                            :fieldPlaceholder="__('modules.businesses.name')">
                        </x-forms.text>
                    </div>
                    <div class="col-md-4">
                        <x-forms.select fieldId="new_field_type" :fieldLabel="__('modules.businesses.type')"
                                    fieldName="new_field_type" fieldRequired="false">
                            <option value="TEXT">{{ _('Text') }}</option>
                            <option value="INTEGER">{{ _('INTEGER') }}</option>
                            <option value="DOCUMENT">{{ _('DOCUMENT') }}</option>
                        </x-forms.select>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-2">
                                <x-forms.checkbox 
                                fieldId="new_field_required"
                                :fieldLabel="__('modules.businesses.required')"
                                fieldName="new_field_required" 
                                :checked=true />
                            </div>
                            <div class="col-md-2">
                                <x-forms.checkbox 
                                fieldId="new_field_admin_only"
                                :fieldLabel="__('modules.businesses.adminOnly')"
                                fieldName="new_field_admin_only" 
                                :checked=false />
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn-primary rounded f-14 py-2 px-4 mt-4" id="add_field">{{ _('Add') }}</button>
                    </div>
                </div>

                <x-forms.custom-field :fields="$fields"></x-forms.custom-field>

                <x-form-actions>
                    <x-forms.button-primary id="save-business-form" class="mr-3" icon="check">
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
        let fieldId = 0;
        const newFieldHtml = (id) => `
                <div class="row p-20" id="field-wrapper-${id}">
                    <div class="col-md-4">
                        <x-forms.text fieldId="fields[${id}][name]" :fieldLabel="__('modules.businesses.name')"
                            fieldName="fields[${id}][name]" fieldRequired="true"
                            :fieldPlaceholder="__('modules.businesses.name')">
                        </x-forms.text>
                    </div>
                    <div class="col-md-4">
                        <x-forms.select fieldId="fields[${id}][type]" :fieldLabel="__('modules.businesses.type')"
                                    fieldName="fields[${id}][type]" fieldRequired="true">
                            <option value="TEXT">{{ _('Text') }}</option>
                            <option value="INTEGER">{{ _('INTEGER') }}</option>
                            <option value="DOCUMENT">{{ _('DOCUMENT') }}</option>
                        </x-forms.select>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-2">
                                <input type="hidden" name="fields[${id}][required]" value="0" />
                                <x-forms.checkbox fieldId="fields[${id}][required]"
                                                :fieldLabel="__('modules.businesses.required')"
                                                fieldName="fields[${id}][required]"
                                                :fieldValue="1" />
                            </div>
                            <div class="col-md-2">
                                <input type="hidden" name="fields[${id}][admin_only]" value="0" />
                                <x-forms.checkbox fieldId="fields[${id}][admin_only]"
                                                :fieldLabel="__('modules.businesses.adminOnly')"
                                                fieldName="fields[${id}][admin_only]"
                                                :fieldValue="1" />
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="button" class="btn-primary rounded f-14 py-2 px-4 mt-4" id="remove_field">{{ _('Remove') }}</button>
                    </div>
                </div>
        `;

        $('#save-more-driver-project-form').click(function() {

            $('#add_more').val(true);

            const url = "{{ route('drivers.store') }}";
            var data = $('#save-business-data-form').serialize();
            saveBusiness(data, url, "#save-more-driver-project-form");
        });

        $('#add_field').click(function() { 
            const fieldName = $('#new_field_name').val();
            const fieldType = $('#new_field_type').val();
            const fieldAdminOnly = $('#new_field_admin_only').prop('checked');
            const fieldRequired = $('#new_field_required').prop('checked');
            const currentFieldId = fieldId++;

            if (!fieldName)
                return alert('Please Enter Field Name.');

            $('#newFields').append(newFieldHtml(currentFieldId));
            $(`#field-wrapper-${currentFieldId} [name="fields[${currentFieldId}][type]"]`).select2();

            const fieldWrapper = $(`#field-wrapper-${currentFieldId}`)
            fieldWrapper.find(`[name="fields[${currentFieldId}][name]"]`).val(fieldName);
            fieldWrapper.find(`[name="fields[${currentFieldId}][type]"]`).val(fieldType).trigger('change');
            fieldWrapper.find(`[name="fields[${currentFieldId}][required]"]`).prop('checked', fieldRequired);
            fieldWrapper.find(`[name="fields[${currentFieldId}][admin_only]"]`).prop('checked', fieldAdminOnly);
            fieldWrapper.find('#remove_field').click(function() {
                fieldWrapper.remove();
            });

            // Reset Form Fields
            $('#add-new-field input').val('');
            $('#new_field_required').prop('checked', true);
            $('#new_field_admin_only').prop('checked', false);
            $('#add-new-field select').each(function() {
                $(this).val($(this).find('option:first').val()).trigger('change');
            })
        });

        $('#save-business-form').click(function() {

            const url = "{{ route('businesses.store') }}";
            var data = $('#save-business-data-form').serialize();
            saveBusiness(data, url, "#save-business-form");

        });

        function saveBusiness(data, url, buttonSelector) {
            $.easyAjax({
                url: url,
                container: '#save-business-data-form',
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
