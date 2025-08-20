<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LanguageSwitcher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $localeRequest = $request->input('locale');
        if (!empty($localeRequest)){
            session()->put('locale', $localeRequest);
        }
        $locale = session()->get('locale');
        if (!empty($locale)) {
            app()->setLocale($locale);
        } else {
            session()->put('locale', "ar");  // Fixed: correct key 'locale'
            app()->setLocale("ar");
        }
        return $next($request);
    }
}
