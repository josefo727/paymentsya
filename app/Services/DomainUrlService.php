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
        $origin = $this->prepareUrl($origin);
        $masterDomain = $this->prepareUrl(config('vtex.master_domain'));
        $productionDomain = $this->prepareUrl(config('vtex.store_domain'));

        if ($this->compareUrls($origin, $masterDomain)) {
            return $masterDomain;
        }

        if ($this->compareUrls($origin, $productionDomain)) {
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

    public function prepareUrl(?string $url): ?string
    {
        if (!$url) {
            return $url;
        }

        return rtrim($url, '/');
    }

    /**
     * @return string
     */
    public function normalizeUrl(string $url): string
    {
        $url = preg_replace("(^https?://)", "", $url);
        $url = preg_replace("(^www\.)", "", $url);
        $url = rtrim($url, '/');
        return $url;
    }

    /**
     * @return bool
     */
    public function compareUrls(string $url1, string $url2): bool
    {
        return $this->normalizeUrl($url1) === $this->normalizeUrl($url2);
    }
}
