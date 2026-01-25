<?php
namespace App\Repositories\Eloquents;
use App\Actions\Localization\GetSupportedLocalesAction;
use App\Repositories\Contracts\LocalizationRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
class LocalizationRepository implements LocalizationRepositoryInterface {
    protected GetSupportedLocalesAction $getSupportedLocalesAction;
    public function __construct(GetSupportedLocalesAction $getSupportedLocalesAction) {
        $this->getSupportedLocalesAction = $getSupportedLocalesAction;
    }

    public function getAllLocales(): array {
        return $this->getSupportedLocalesAction->execute();
    }

    public function getActiveLocales(): array {
        $locales = $this->getSupportedLocalesAction->execute();
        return array_filter($locales, function ($locale) {
            return $locale['is_active'] === true;
        });
    }

    public function getInactiveLocales(): array {
        $locales = $this->getSupportedLocalesAction->execute();
        return array_filter($locales, function ($locale) {
            return $locale['is_active'] === false;
        });
    }

    public function paginateArray(array $items, int $perPage = 10, ?int $currentPage = null, array $options = []): LengthAwarePaginator {
        $currentPage = $currentPage ?: (LengthAwarePaginator::resolveCurrentPage() ?: 1);
        $collection = new Collection($items);
        $currentPageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        return new LengthAwarePaginator(
            $currentPageItems,
            $collection->count(),
            $perPage,
            $currentPage,
            $options
        );
    }

    public function toggleStatus(string $key): array {
        $configPath = config_path('laravellocalization.php');
        $steps = [];
        try {
            if (!file_exists($configPath)) {
                throw new \Exception("Config file not found at {$configPath}");
            }
            $config = require $configPath;
            if (!isset($config['supportedLocales'][$key])) {
                throw new \Exception("Language key not found: $key");
            }

            $currentStatus = $config['supportedLocales'][$key]['status'] ?? '0';
            $newStatus = $currentStatus === '1' ? '0' : '1';
            $config['supportedLocales'][$key]['status'] = $newStatus;
            $content = "<?php\n\nreturn " . var_export($config, true) . ";\n";
            if (false === file_put_contents($configPath, $content)) {
                throw new \Exception("Failed to write config file at {$configPath}");
            }
            $steps[] = [
                'step' => 'Update status in config',
                'status' => 'success',
                'message' => "Language '$key' status updated to " . ($newStatus === '1' ? 'active' : 'inactive')
            ];
            try {
                \Artisan::call('config:clear');
                $steps[] = [
                    'step' => 'Clear config cache',
                    'status' => 'success',
                    'message' => 'Config cache cleared successfully'
                ];
            } catch (\Exception $e) {
                $steps[] = [
                    'step' => 'Clear config cache',
                    'status' => 'warning',
                    'message' => 'Could not clear config cache: ' . $e->getMessage()
                ];
            }

            return [
                'key' => $key,
                'status' => $newStatus,
                'newStatus' => $newStatus,
                'steps' => $steps
            ];
        } catch (\Exception $e) {
            $steps[] = [
                'step' => 'Error',
                'status' => 'failed',
                'message' => $e->getMessage()
            ];
            return [
                'key' => $key,
                'status' => null,
                'newStatus' => null,
                'steps' => $steps
            ];
        }
    }
}