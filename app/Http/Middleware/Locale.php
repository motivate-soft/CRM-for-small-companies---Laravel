<?php

namespace App\Http\Middleware;

use App\User;
use App\Models\Language;
use Closure;
use Illuminate\Support\Facades\App;

class Locale
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
        if(backpack_auth()->check()) {
            if(backpack_user()->role == User::ROLE_COMPANY && backpack_user()->company->language) {
                app()->setLocale(backpack_user()->company->language->abbr);
                date_default_timezone_set(backpack_user()->company->timezone);
            }
            if(backpack_user()->role == User::ROLE_EMPLOYEE && backpack_user()->employee->language) {
                app()->setLocale(backpack_user()->employee->language->abbr);
                date_default_timezone_set(backpack_user()->employee->company->timezone);
            }
        }else{
            $userLangs = preg_split('/,|;/', $request->server('HTTP_ACCEPT_LANGUAGE'));
            $main_lang = $userLangs[0];
            $main_lang = explode('-',$main_lang)[0];
            $lang_list = Language::pluck('abbr');
            app()->setLocale('en');
            foreach ($lang_list as $lang){
                if($lang == $main_lang){
                    app()->setLocale($main_lang);
                    break;
                }
            }
        }

        return $next($request);
    }
}
