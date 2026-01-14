<?php
namespace App\Services\Auth\Strategy;
use App\Models\Admin;
class AdminAuthStrategy extends BaseAuthStrategy {
    public function __construct() {
        parent::__construct('admin', Admin::class);
    }
}
