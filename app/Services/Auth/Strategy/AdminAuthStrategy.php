<?php
namespace App\Services\Auth\Strategy;
class AdminAuthStrategy extends BaseAuthStrategy {
    public function __construct() {
        parent::__construct('admin');
    }

    protected function model(): string {
        return \App\Models\Admin::class;
    }
}