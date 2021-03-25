<?php

declare(strict_types=1);

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2021, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Portal\Http;

use LC\Portal\Http\Exception\HttpException;
use LC\Portal\Json;
use LC\Portal\TplInterface;

class Service
{
    private ?TplInterface $tpl;

    private array $routes;

    /** @var array<string,BeforeHookInterface> */
    private array $beforeHooks;

    public function __construct(?TplInterface $tpl = null)
    {
        $this->tpl = $tpl;
        $this->routes = [];
        $this->beforeHooks = [];
    }

    public function addBeforeHook(string $name, BeforeHookInterface $beforeHook): void
    {
        $this->beforeHooks[$name] = $beforeHook;
    }

    public function addRoute(string $requestMethod, string $pathInfo, callable $callback): void
    {
        $this->routes[$pathInfo][$requestMethod] = $callback;
    }

    public function get(string $pathInfo, callable $callback): void
    {
        $this->addRoute('GET', $pathInfo, $callback);
    }

    public function post(string $pathInfo, callable $callback): void
    {
        $this->addRoute('POST', $pathInfo, $callback);
    }

    public function addModule(ServiceModuleInterface $module): void
    {
        $module->init($this);
    }

    public function run(Request $request): Response
    {
        try {
            // before hooks
            $hookData = [];
            foreach ($this->beforeHooks as $k => $v) {
                $hookResponse = $v->executeBefore($request, $hookData);
                // if we get back a Response object, return it immediately
                if ($hookResponse instanceof Response) {
                    return $hookResponse;
                }

                $hookData[$k] = $hookResponse;
            }

            $requestMethod = $request->getRequestMethod();
            $pathInfo = $request->getPathInfo();

            if (!\array_key_exists($pathInfo, $this->routes)) {
                throw new HttpException(sprintf('"%s" not found', $pathInfo), 404);
            }
            if (!\array_key_exists($requestMethod, $this->routes[$pathInfo])) {
                throw new HttpException(sprintf('method "%s" not allowed', $requestMethod), 405, ['Allow' => implode(',', array_keys($this->routes[$pathInfo]))]);
            }

            return $this->routes[$pathInfo][$requestMethod]($request, $hookData);
        } catch (HttpException $e) {
            if ($request->isBrowser()) {
                if (null === $this->tpl) {
                    // template not available
                    $response = new Response((int) $e->getCode(), 'text/plain');
                    $response->setBody(sprintf('%d: %s', (int) $e->getCode(), $e->getMessage()));
                } else {
                    // template available
                    $response = new Response((int) $e->getCode(), 'text/html');
                    $response->setBody(
                        $this->tpl->render(
                            'errorPage',
                            [
                                'code' => (int) $e->getCode(),
                                'message' => $e->getMessage(),
                            ]
                        )
                    );
                }
            } else {
                // not a browser
                $response = new Response((int) $e->getCode(), 'application/json');
                $response->setBody(Json::encode(['error' => $e->getMessage()]));
            }

            foreach ($e->getResponseHeaders() as $key => $value) {
                $response->addHeader($key, $value);
            }

            return $response;
        }
    }

    /**
     * @param array<string,array<string>> $whiteList
     */
    public static function isWhitelisted(Request $request, array $whiteList): bool
    {
        if (!\array_key_exists($request->getRequestMethod(), $whiteList)) {
            return false;
        }

        return \in_array($request->getPathInfo(), $whiteList[$request->getRequestMethod()], true);
    }
}
