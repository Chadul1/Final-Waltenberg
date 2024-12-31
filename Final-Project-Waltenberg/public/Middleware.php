<?php 

namespace App\Middleware;

//A class that helps with middleware usage.
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as SlimResponse;

// Middleware to check for required roles
function roleMiddleware(array $allowedRoles) {
    return function (Request $request, $handler) use ($allowedRoles) {
        // Assume we get the user's roles from a session or a similar source
        $role = $_SESSION['user']['UserRole'] ?? ['guest']; // Example: ['admin', 'editor']

        // Check if the user has any of the allowed roles
        $hasRole = false;
        if (in_array($role, $allowedRoles)) {
            $hasRole = true;
        }

        if ($hasRole) {
            // User has the required role; continue to the next handler
            return $handler->handle($request);
        } else {
            // User does not have permission; redirect to an "access denied" page or login
            $response = new SlimResponse();
            return $response->withHeader('Location', '/access-denied')->withStatus(302);
        }
    };
}