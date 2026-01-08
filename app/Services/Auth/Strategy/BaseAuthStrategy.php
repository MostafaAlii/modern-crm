<?php
namespace App\Services\Auth\Strategy;
use Illuminate\Support\Facades\{Auth, Hash};
use App\Services\Auth\AuthStrategyInterface;
abstract class BaseAuthStrategy implements AuthStrategyInterface {
    protected string $guard;
    public function __construct(string $guard) {
        $this->guard = $guard;
    }

    abstract protected function model(): string;
    public function attempt(array $credentials): mixed {
        $userModel = $this->model();
        $user = $userModel::where('email', $credentials['email'])->first();
        if (!$user) return null;
        if (!Hash::check($credentials['password'], $user->password)) return null;
        return $user;
    }

    public function loginUser(mixed $user): void {
        Auth::guard($this->guard)->login($user);
    }

    public function logout(): void {
        Auth::guard($this->guard)->logout();
    }
}
