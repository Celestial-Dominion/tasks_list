<?php

namespace App\Http\Middleware;

use App\Models\ProjectParticipant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProjectParticipant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $project = $request->route('project');
        $userId = auth()->id();

        $participant = ProjectParticipant::where('project_id', $project->id)
            ->where('user_id', $userId)
            ->first();

        if (!$participant) {
            abort(403, 'Unauthorized. User does not belong to this project.');
        }

        return $next($request);
    }
}
