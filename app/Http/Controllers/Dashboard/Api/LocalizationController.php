<?php

namespace App\Http\Controllers\Dashboard\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocaleResource;
use App\Models\Concerns\ApiResponseTrait;
use App\Services\Dashboard\LocalizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocalizationController extends Controller {
    use ApiResponseTrait;
    protected LocalizationService $localizationService;
    public function __construct(LocalizationService $localizationService) {
        $this->localizationService = $localizationService;
    }

    public function index(Request $request): JsonResponse {
        $status = $request->query('status');
        $perPage = $request->query('per_page', 10);
        $locales = $this->localizationService->getPaginatedLocales($status, (int)$perPage);
        $locales->getCollection()->transform(function ($locale) {
            return (new LocaleResource($locale))->resolve();
        });
        $message = $this->getMessageByStatus($status);
        return $this->paginatedResponse($locales, $message);
    }

    public function active(Request $request): JsonResponse {
        $perPage = $request->query('per_page', 10);
        $locales = $this->localizationService->getPaginatedLocales('active', (int)$perPage);
        $locales->getCollection()->transform(function ($locale) {
            return (new LocaleResource($locale))->resolve();
        });
        return $this->paginatedResponse($locales, 'Active locales retrieved successfully');
    }

    public function inactive(Request $request): JsonResponse {
        $perPage = $request->query('per_page', 10);
        $locales = $this->localizationService->getPaginatedLocales('inactive', (int)$perPage);
        $locales->getCollection()->transform(function ($locale) {
            return (new LocaleResource($locale))->resolve();
        });
        return $this->paginatedResponse($locales, 'Inactive locales retrieved successfully');
    }

    public function toggleStatus(Request $request): JsonResponse{
         $request->validate(['key' => 'required|string']);
        try {
            $result = $this->localizationService->toggleLocaleStatus($request->key);
            return $this->successResponse(
                $result,
                'Language status updated and post-toggle steps executed'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    private function getMessageByStatus(?string $status): string {
        return match ($status) {
            'active' => 'Active locales retrieved successfully',
            'inactive' => 'Inactive locales retrieved successfully',
            default => 'All locales retrieved successfully',
        };
    }
}