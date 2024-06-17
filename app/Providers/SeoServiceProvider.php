<?php
namespace Coyote\Providers;

use Coyote\Domain\Seo\Schema;
use Coyote\View\Twig\TwigLiteral;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
//
use Illuminate\View\Factory;

class SeoServiceProvider extends \Coyote\Providers\Neon\ServiceProvider
{
    public function boot(): void
    {
        /** @var Factory $view */
        $view = $this->app['view'];
        $view->composer('layout', function (View $view): void {
            $view->with([
                'schema_organization' => TwigLiteral::fromHtml(new Schema(new Schema\Organization())),
                'meta_robots'         => $this->metaRobots(),
                'meta_canonical'      => $this->metaCanonicalForRequest(),
            ]);
        });
    }

    private function metaRobots(): string
    {
        /** @var Request $request */
        $request = $this->app['request'];
        if ($request->getHost() === '4programmers.dev') {
            return 'noindex,nofollow';
        }
        if ($request->getPathInfo() === '/Mikroblogi') {
            return 'noindex,follow';
        }
        if ($request->getPathInfo() === '/Search') {
            return 'noindex,follow';
        }
        if (\in_array($request->getPathInfo(), ['/Forum/Interesting', '/Pastebin'])) {
            return 'noindex,nofollow';
        }
        return 'index,follow';
    }

    private function metaCanonicalForRequest(): ?string
    {
        /** @var Request $request */
        $request = $this->app['request'];
        return $this->metaCanonical($request);
    }

    private function metaCanonical(Request $request): ?string
    {
        if ($request->getPathInfo() === '/Mikroblogi') {
            return null;
        }
        return 'https://' .
            $request->getHost() .
            $this->canonicalPath($request->getPathInfo()) .
            $this->queryString($request);
    }

    private function canonicalPath(string $path): string
    {
        if (\starts_with($path, '/Praca/Technologia')) {
            return '/Praca';
        }
        return $path;
    }

    private function queryString(Request $request): string
    {
        return \rTrim('?' . \http_build_query($this->queryStringParams($request)), '?');
    }

    private function queryStringParams(Request $request): array
    {
        if (!$this->allowQueryParam($request)) {
            return [];
        }
        $queryParams = $this->queryParams($request);
        if (\array_key_exists('page', $queryParams)) {
            return ['page' => $queryParams['page']];
        }
        return [];
    }

    private function allowQueryParam(Request $request): bool
    {
        $uri = $request->getPathInfo();
        if ($uri === '/Forum') {
            return false;
        }
        return \str_starts_with($uri, '/Forum') || \str_starts_with($uri, '/Praca');
    }

    private function queryParams(Request $request): array
    {
        \parse_str($request->getQueryString(), $queryParams);
        return $queryParams;
    }
}
