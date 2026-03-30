<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait AjaxPagination
{
    protected function estAjax(Request $request): bool
    {
        return $request->ajax() ||
               $request->wantsJson() ||
               $request->header('X-Requested-With') === 'XMLHttpRequest';
    }
}