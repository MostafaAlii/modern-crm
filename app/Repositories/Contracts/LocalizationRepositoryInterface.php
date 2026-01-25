<?php
namespace App\Repositories\Contracts;
use Illuminate\Pagination\LengthAwarePaginator;
interface LocalizationRepositoryInterface {
    public function getAllLocales(): array;
    public function getActiveLocales(): array;
    public function getInactiveLocales(): array;
    public function paginateArray(array $items, int $perPage = 10, ?int $currentPage = null, array $options = []): LengthAwarePaginator;
    public function toggleStatus(string $key): array;
}