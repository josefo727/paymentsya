<?php

namespace App\Services;

use Illuminate\Http\Request;

class DomainUrlService
{
    /*
     * @return string|null
     * @param Request $request
     */
    public function generate(Request $request): string|null
    {
        $origin = $request->headers->get('Origin');
        $masterDomain = config('vtex.master_domain');
        $productionDomain = config('vtex.store_domain');

        if ($origin === $masterDomain) {
            return $masterDomain;
        }

        if ($origin === $productionDomain) {
            return $productionDomain;
        }

        if ($this->isWorkspace($origin, $masterDomain)) {
            return $origin;
        }

        if (!$origin) {
            return $masterDomain;
        }

        return null;
    }

    /**
     * @param mixed $origin
     * @param mixed $master
     */
    public function isWorkspace($origin, $master): bool
    {
        if (preg_match('/\.myvtex\.com$/', $origin) && strpos($origin, '--') !== false) {
            $sub = preg_replace('/^.*?--/', '', $origin);
            if (strpos($master, $sub) !== false) {
                return true;
            }
        }
        return false;
    }
}
