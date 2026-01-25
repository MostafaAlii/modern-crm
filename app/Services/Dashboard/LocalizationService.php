<?php

namespace App\Services\Dashboard;

use App\Repositories\Contracts\LocalizationRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class LocalizationService
{
    protected LocalizationRepositoryInterface $localizationRepository;

    public function __construct(LocalizationRepositoryInterface $localizationRepository)
    {
        $this->localizationRepository = $localizationRepository;
    }

    /**
     * Get all locales
     *
     * @return array
     */
    public function getAllLocales(): array
    {
        return $this->localizationRepository->getAllLocales();
    }

    /**
     * Get active locales only
     *
     * @return array
     */
    public function getActiveLocales(): array
    {
        return $this->localizationRepository->getActiveLocales();
    }

    /**
     * Get inactive locales only
     *
     * @return array
     */
    public function getInactiveLocales(): array
    {
        return $this->localizationRepository->getInactiveLocales();
    }

    /**
     * Get locales by status
     *
     * @param string|null $status
     * @return array
     */
    public function getLocalesByStatus(?string $status = null): array
    {
        if ($status === 'active') {
            return $this->getActiveLocales();
        }

        if ($status === 'inactive') {
            return $this->getInactiveLocales();
        }

        return $this->getAllLocales();
    }

    /**
     * Get paginated locales
     *
     * @param string|null $status
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedLocales(?string $status = null, int $perPage = 10): LengthAwarePaginator
    {
        $locales = $this->getLocalesByStatus($status);

        return $this->localizationRepository->paginateArray($locales, $perPage);
    }

    public function toggleLocaleStatus(string $key): array
    {
        $result = $this->localizationRepository->toggleStatus($key);
        if (!isset($result['status'])) {
            throw new \Exception('Failed to toggle locale status');
        }
        $event = new \App\Events\LocaleStatusToggled($result['key'], $result['newStatus']);
        $listener = new \App\Listeners\HandleLocalePostToggle();
        $steps = $listener->handle($event);
        return [
            'key' => $result['key'],
            'status' => $result['status'],
            'steps' => $steps,
        ];
    }
}