<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\GameSession;

class CheckSavedGame
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for saved games on trivia routes
        if ($request->routeIs('trivia.*') && !$request->routeIs('trivia.index')) {
            $gameToken = $request->cookie('trivia_game_token');
            
            if ($gameToken) {
                $savedGame = GameSession::findActiveGameByToken($gameToken);
                
                // If we have a valid saved game but no session game state, restore it
                if ($savedGame && $savedGame->game_state && !$request->session()->has('trivia')) {
                    $gameState = $savedGame->getRestoredGameState();
                    $request->session()->put('trivia', $gameState);
                }
                
                // If the saved game is no longer valid, clear the cookie
                if (!$savedGame) {
                    cookie()->queue(cookie()->forget('trivia_game_token'));
                }
            }
        }
        
        return $next($request);
    }
}
