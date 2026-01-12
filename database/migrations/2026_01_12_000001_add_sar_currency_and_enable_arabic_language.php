<?php

use App\Models\Company;
use App\Models\Currency;
use App\Models\LanguageSetting;
use App\Models\SuperAdmin\GlobalCurrency;
use App\Scopes\CompanyScope;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        $sar = [
            'currency_name' => 'Saudi Riyal',
            'currency_symbol' => '﷼',
            'currency_code' => 'SAR',
            'exchange_rate' => 1,
            'currency_position' => 'left',
            'no_of_decimal' => 2,
            'thousand_separator' => ',',
            'decimal_separator' => '.',
            'is_cryptocurrency' => 'no',
            'status' => 'enable',
        ];

        $globalSar = GlobalCurrency::withTrashed()->firstOrNew(['currency_code' => $sar['currency_code']]);

        if ($globalSar->trashed()) {
            $globalSar->restore();
        }

        $globalSar->currency_name = $sar['currency_name'];
        $globalSar->currency_symbol = $sar['currency_symbol'];
        $globalSar->exchange_rate = $sar['exchange_rate'];
        $globalSar->currency_position = $sar['currency_position'];
        $globalSar->no_of_decimal = $sar['no_of_decimal'];
        $globalSar->thousand_separator = $sar['thousand_separator'];
        $globalSar->decimal_separator = $sar['decimal_separator'];
        $globalSar->is_cryptocurrency = $sar['is_cryptocurrency'];
        $globalSar->status = $sar['status'];
        $globalSar->save();

        Company::cursor()->each(function (Company $company) use ($sar) {
            $currency = Currency::withoutGlobalScope(CompanyScope::class)
                ->firstOrNew([
                    'company_id' => $company->id,
                    'currency_code' => $sar['currency_code'],
                ]);

            $currency->currency_name = $sar['currency_name'];
            $currency->currency_symbol = $sar['currency_symbol'];
            $currency->exchange_rate = $sar['exchange_rate'];
            $currency->currency_position = $sar['currency_position'];
            $currency->no_of_decimal = $sar['no_of_decimal'];
            $currency->thousand_separator = $sar['thousand_separator'];
            $currency->decimal_separator = $sar['decimal_separator'];
            $currency->is_cryptocurrency = $sar['is_cryptocurrency'];
            $currency->save();
        });

        $arabic = LanguageSetting::firstOrNew(['language_code' => 'ar']);
        $arabic->language_name = $arabic->language_name ?: 'Arabic';
        $arabic->flag_code = $arabic->flag_code ?: 'sa';
        $arabic->status = 'enabled';
        $arabic->save();

        cache()->forget('language_setting');
        cache()->forget('language_setting_ar');
    }

    public function down(): void
    {
        // Keeping currency and language data intact to avoid removing active selections.
    }
};
