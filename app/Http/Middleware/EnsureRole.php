<?php
 
namespace App\Http\Middleware;
 
use Illuminate\Http\Request;
 
class EnsureRole
{
    public function handle(Request $request, \Closure $next, ...$roles)
    {
        $user = $request->user();
 
        if (!$user) {
            abort(403);
        }
 
        if (!empty($roles) && !$this->matchesAnyRole((string) $user->role, $roles)) {
            abort(403);
        }
 
        return $next($request);
    }

    private function matchesAnyRole(string $userRole, array $requiredRoles): bool
    {
        $normalizedUserRoles = $this->expandRoleAliases($userRole);

        foreach ($requiredRoles as $requiredRole) {
            $normalizedRequiredRoles = $this->expandRoleAliases((string) $requiredRole);

            if (array_intersect($normalizedUserRoles, $normalizedRequiredRoles)) {
                return true;
            }
        }

        return false;
    }

    private function expandRoleAliases(string $role): array
    {
        if ($role === 'admin' || $role === 'staff') {
            return ['admin', 'staff'];
        }

        return [$role];
    }
}