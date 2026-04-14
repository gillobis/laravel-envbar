<?php

namespace Gillobis\Envbar\Http\Middleware;

use Closure;
use Gillobis\Envbar\EnvbarManager;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectEnvbar
{
    public function __construct(protected EnvbarManager $manager) {}

    /**
     * @throws BindingResolutionException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldInject($request, $response)) {
            $html = $this->manager->render();
            $content = str_replace('</body>', $html.'</body>', $response->getContent());
            $response->setContent($content);
        }

        return $response;
    }

    /**
     * Injection conditions (shouldInject):
     * - The response is HTML (Content-Type contains "text/html")
     * - The response contains a </body> tag (where to inject the bar)
     * - The bar is enabled for the current environment (manager->isEnabled)
     * - It is not an AJAX request (unless configured otherwise)
     */
    private function shouldInject(Request $request, Response $response): bool
    {
        return str_contains($response->headers->get('Content-Type'), 'text/html')
            && str_contains($response->getContent(), '</body>')
            && ! $request->ajax()
            && $this->manager->isEnabled();
    }
}
