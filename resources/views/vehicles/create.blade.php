@extends('layouts.app')


@section('content')
    
<div class="content-wrapper">
    @include($view)
</div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            // Function to toggle the visibility of the replacement fields
            function toggleReplacementFields() {
                var status = $('#status').val();
                if (status == '3') {
                    $('#replacementRow').removeClass('d-none');
                } else {
                    $('#replacementRow').addClass('d-none');
                }
            }

            // Bind the function to the change event of the status select element
            $('#status').on('change', function() {
                toggleReplacementFields();
            });

            // Initialize visibility on page load
            toggleReplacementFields();
        });
    </script>
@endpush