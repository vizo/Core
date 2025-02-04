<?php
namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class PublicLocale
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $firstSegment = Request::segment(1);
        $locale = config('app.locale');

        if (in_array($firstSegment, config('translatable.locales'))) {
            $locale = $firstSegment;
        }

        // Throw a 404 if website in this language is offline
        if (!config('typicms.' . $locale . '.status')) {
            abort(404);
        }

        // Remove preview param if no admin user connected
        if (Input::get('preview') && !Auth::check()) {
            return Redirect::to(Request::path());
        }

        return $next($request);
    }
}
