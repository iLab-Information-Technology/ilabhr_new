@extends('layouts.public')

<style>
    .two-factor-bg {
        background-color: #ffffff !important;
    }
</style>
<!-- SETTINGS START -->
<div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4 ">

    <div class="row">

        <div class="col-lg-12">

            <x-alert type="secondary" icon="info-circle">
                @lang('modules.twofactor.twoFaInfo')
            </x-alert>

            <div class="row">
                <div class="col-lg-12 mt-3">
                    <div class="border-grey p-4 border-top-0 rounded-bottom">
                        <div class="row justify-content-center">
                            <div class="col-md-1 d-flex justify-content-center align-self-baseline">
                                <img src="{{ asset('img/google-authenticator-2.svg') }}" width="27" alt="">
                            </div>
                            <div class="col-md-11">
                                <h6>@lang('modules.twofactor.setupGoogleAuthenticator')
                                    @if ($user->two_fa_verify_via == 'google_authenticator' || $user->two_fa_verify_via == 'both')
                                        @if ($user->two_factor_confirmed)
                                            <span class="badge badge-success ml-2">@lang('app.active')</span>
                                        @else
                                            <span class="badge badge-warning ml-2">@lang('modules.twofactor.validate2FA')
                                                @lang('app.pending')</span>
                                        @endif
                                    @endif
                                </h6>
                                <p class="mb-4 mt-2 f-14 text-dark-grey">
                                    @lang('messages.enable2FAUsingAuthenticator')
                                </p>

                                @if ($user->two_factor_secret)
                                    <p class="f-w-500">@lang('modules.twofactor.2faBarcode')</p>
                                    <span class="p-2 border rounded w-100 d-table-cell two-factor-bg">
                                        {!! $user->twoFactorQrCodeSvg() !!}
                                    </span>
                                    <div class="my-4 f-12 text-lightest">
                                        <span class="badge badge-primary">@lang('app.note')</span>
                                        @lang('modules.twofactor.2faAppWarning')
                                    </div>
                                @endif

                                @if ($user->two_fa_verify_via == 'google_authenticator' || $user->two_fa_verify_via == 'both')
                                    @if ($user->two_factor_confirmed)
                                        <x-forms.button-secondary class="change-2fa-status"
                                            data-method="google_authenticator" data-status="disable">
                                            @lang('app.disable')
                                        </x-forms.button-secondary>

                                        <x-forms.button-cancel class="ml-3" :link="route('2fa_codes_download')">
                                            @lang('app.downloadRecoveryCode')
                                        </x-forms.button-cancel>

                                        <x-forms.button-cancel class="ml-3" id="regenerate-codes">
                                            @lang('app.regenerateRecoveryCode')
                                        </x-forms.button-cancel>
                                    @else
                                        <x-forms.button-primary class="validate-2fa" data-toggle="modal"
                                            data-target="#twoFactorModal">
                                            @lang('modules.twofactor.validate2FA')
                                        </x-forms.button-primary>

                                        <x-forms.button-secondary class="change-2fa-status ml-3"
                                            data-method="google_authenticator" data-status="disable">
                                            @lang('app.disable')
                                        </x-forms.button-secondary>
                                    @endif
                                @else
                                    <x-forms.button-primary class="change-2fa-status" data-method="google_authenticator"
                                        data-status="enable">
                                        @lang('app.enable')
                                    </x-forms.button-primary>
                                @endif



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="twoFactorModal" tabindex="-1" role="dialog" aria-labelledby="modelHeading" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modelHeading">@lang('app.authenticationRequired')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <x-form id="reset-password-form" class="ajax-form" method="POST">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <x-forms.label class="mt-3" fieldId="password" :fieldLabel="__('app.twoFactorCode')">
                            </x-forms.label>
                            <x-forms.input-group>
                                @includeIf('sections.2fa-input-field')
                            </x-forms.input-group>
                        </div>
                    </div>
                </x-form>
            </div>
            <div class="modal-footer">
                <x-forms.button-cancel data-dismiss="modal"
                    class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
                <x-forms.button-primary id="submit-login" icon="check"
                    class="otp-submit">@lang('modules.twofactor.validate2FA')</x-forms.button-primary>
            </div>
        </div>
    </div>
</div>

<!-- SETTINGS END -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@includeIf('sections.2fa-js')
<script>
    $('#regenerate-codes').click(function() {
        let url = "/user/two-factor-recovery-codes";
        let token = "{{ csrf_token() }}";
        let method = 'POST';

        $.easyAjax({
            url: url,
            type: "POST",
            data: {
                '_token': token,
                '_method': method
            },
            success: function(response) {
                window.location.reload();
            }
        });
    });

    $('.change-2fa-status').click(function() {
        let method = $(this).data('method');
        let status = $(this).data('status');

        let url = "{{ route('verify_2fa_password') }}";
        url = url + '?method=' + method + '&status=' + status;

        $(MODAL_DEFAULT + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_DEFAULT, url);
    });

    $('.validate-2fa').click(function() {
        $(MODAL_DEFAULT + ' ' + MODAL_HEADING).html('...');
    });

    $('.validate-email-2fa').click(function() {
        let url = "{{ route('two-fa-settings.validate_email_confirm') }}";

        $(MODAL_DEFAULT + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_DEFAULT, url);
    });
    $('#submit-login').click(function() {

        var url = "{{ route('two-fa-settings.confirm') }}";
        $.easyAjax({
            url: url,
            container: '#reset-password-form',
            disableButton: true,
            blockUI: true,
            buttonSelector: "#submit-login",
            type: "POST",
            data: $('#reset-password-form').serialize(),
            success: function(response) {
                if (response.status == 'success') {
                    window.location.reload();
                }
            }
        })
    });
</script>
