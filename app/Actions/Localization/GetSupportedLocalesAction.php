<?php
namespace App\Actions\Localization;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
class GetSupportedLocalesAction {
    public function execute(): array {
        $supportedLocales = LaravelLocalization::getSupportedLocales();
        $locales = [];
        foreach ($supportedLocales as $key => $locale) {
            $status = isset($locale['status']) ? (string)$locale['status'] : '0';
            $locales[] = [
                'code' => $key,
                'name' => $locale['name'] ?? '',
                'native' => $locale['native'] ?? '',
                'script' => $locale['script'] ?? '',
                'regional' => $locale['regional'] ?? '',
                'status' => $status,
                'is_active' => $status === '1',
            ];
        }
        return $locales;
    }
}