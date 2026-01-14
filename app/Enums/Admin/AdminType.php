<?php

namespace App\Enums\Admin;

enum AdminType: string
{
    case OWNER = 'owner';
    case COMPANY_ADMIN = 'company_admin';
    case ADMIN = 'admin';

    public static function label($value): string
    {
        return match ($value) {
            self::OWNER => trans('dashboard/general.owner'),
            self::COMPANY_ADMIN => trans('dashboard/general.company_admin'),
            self::ADMIN => trans('dashboard/general.branch_admin'),
            default => trans('dashboard/general.not_type_assigned'),
        };
    }

    public static function badge($value): string
    {
        return match ($value) {
            self::OWNER => '<span class="badge bg-primary">' . trans('dashboard/general.owner') . '</span>',
            self::COMPANY_ADMIN => '<span class="badge bg-info">' . trans('dashboard/general.company_admin') . '</span>',
            self::ADMIN => '<span class="badge bg-secondary">' . trans('dashboard/general.branch_admin') . '</span>',
            default => '<span class="badge bg-default">' . trans('dashboard/general.not_type_assigned') . '</span>',
        };
    }
}
