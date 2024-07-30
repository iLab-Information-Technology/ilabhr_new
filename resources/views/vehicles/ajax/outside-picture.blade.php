<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">

<div class="modal-header">
    <h5 class="modal-title">@lang('app.menu.addOutsidePicture')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-form id="save-other-report-data-form" method="PUT" class="ajax-form">
            <div class="row">
                <div class="col-lg-12">
                    <x-forms.file-multiple allowedFileExtensions="png jpg jpeg svg pdf doc docx"
                        class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.vehicles.insidePictures')" fieldName="outside_images[]"
                        fieldId="outside-images" multiple>
                    </x-forms.file-multiple>
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
    Dropzone.autoDiscover = false;

    var myDropzone = new Dropzone("#outside-images", {
        url: "{{ route('vehicles.uploadImages', $vehicle->id) }}",
        method: "POST",
        paramName: "outside_images[]",
        maxFilesize: 3, // MB
        maxFiles: 5,
        acceptedFiles: "image/*",
        autoProcessQueue: true,
        addRemoveLinks: true,
        init: function() {
            var submitButton = document.querySelector("#save-other-report-form");
            var dropzone = this;

            @php($outsideImages = $vehicle->images()->where('type', 'outside')->get())

                // Add previously uploaded images to Dropzone
                @foreach ($outsideImages as $image)
                    var mockFile = {
                        name: "{{ $image->image }}",
                        size: 12345
                    };
                    dropzone.emit("addedfile", mockFile);
                    dropzone.emit("thumbnail", mockFile,
                        "{{ asset('user-uploads/vehicle-images/' . $image->image) }}");
                    dropzone.emit("complete", mockFile);
                @endforeach

            submitButton.addEventListener("click", function(event) {
                event.preventDefault();
                event.stopPropagation();

                if (dropzone.getQueuedFiles().length > 0) {
                    dropzone.processQueue();
                } else {
                    submitForm();
                }
            });

            this.on("queuecomplete", function() {
                // submitForm();
            });

            this.on("error", function(file, response) {
                console.log("Error uploading file:", response);
            });

            function submitForm() {
                var formData = new FormData(document.querySelector("#save-other-report-data-form"));
                var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                formData.append('_token', csrfToken);
                formData.append('_method', 'PUT');

                // Add uploaded file names to form data
                dropzone.getAcceptedFiles().forEach(function(file) {
                    formData.append('outside_images[]', file);
                });

                $.ajax({
                    url: "{{ route('vehicles.update', $vehicle->id) }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status === 'success') {
                            window.location.reload();
                        }
                    },
                    error: function(xhr) {
                        console.log("Error in AJAX request:", xhr.responseText);
                    }
                });
            }
        }
    });
</script>
