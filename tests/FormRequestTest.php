<?php

namespace LaravelFormRequest\Tests;

use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\ValidationException;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class FormRequestTest extends TestCase
{

    /**
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = Container::getInstance();
        $this->container->bind(\Illuminate\Contracts\Validation\Factory::class, \Illuminate\Validation\Factory::class);
        $this->container->singleton(\Illuminate\Contracts\Translation\Translator::class, function () {
            $filesystem = new Filesystem();
            $fileLoader = new FileLoader($filesystem, '');
            $translator = new Translator($fileLoader, '');
            return $translator;
        });

        $this->router = new Router(m::mock(Dispatcher::class), $this->container);
    }

    protected function tearDown(): void
    {
        m::close();
    }

    public function testControllerAction()
    {
        $this->router->get('users', UserController::class . '@index');

        $route = $this->getRoute();

        $request = UserFormRequest::create('users', 'GET')
            ->setContainer($this->container)
            ->setRedirector($this->createMockRedirector())
            ->setRouteResolver(function () use ($route) {
                return $route;
            });

        $route->bind($request)->setParameter(0, $request);

        try {

            $route->run();

            $this->assertTrue(false);

        } catch (ValidationException $exception) {

            $this->assertEquals($exception->errors(), [
                'name' => [
                    'The name field is required in index scenario.'
                ]
            ]);

        }
    }

    public function testClosure()
    {

        $this->router->get('users', function (UserFormRequest $closureRequest) {
            $closureRequest->validateResolved();
        });

        $route = $this->getRoute();

        $request = UserFormRequest::create('users', 'GET')
            ->setContainer($this->container)
            ->setRedirector($this->createMockRedirector())
            ->setRouteResolver(function () use ($route) {
                return $route;
            });

        $route->bind($request)->setParameter(0, $request);

        try {

            $route->run();

            $this->assertTrue(false);

        } catch (ValidationException $exception) {

            $this->assertEquals($exception->errors(), [
                'name' => [
                    'The name field is required in closure scenario.'
                ]
            ]);

        }

    }

    public function testDefault()
    {

        $message = 'Undefined scenario use the default scenario.';

        $this->router->get('users', function (UserFormRequest $undefinedRequest) use ($message) {
            $undefinedRequest->validateResolved();
            return $message;
        });

        $route = $this->getRoute();

        $request = UserFormRequest::create('users', 'GET')
            ->setContainer($this->container)
            ->setRedirector($this->createMockRedirector())
            ->setRouteResolver(function () use ($route) {
                return $route;
            });

        $route->bind($request)->setParameter(0, $request);

        $this->assertEquals($route->run(), $message);

    }

    /**
     * Get the last route registered with the router.
     *
     * @return \Illuminate\Routing\Route
     */
    protected function getRoute()
    {
        return last($this->router->getRoutes()->get());
    }

    /**
     * Create a mock redirector.
     *
     * @return \Illuminate\Routing\Redirector
     */
    protected function createMockRedirector()
    {
        $redirector = m::mock(Redirector::class);

        $generator = $this->createMockUrlGenerator();

        $redirector->shouldReceive('getUrlGenerator')->zeroOrMoreTimes()
            ->andReturn($generator);

        $response = $this->createMockRedirectResponse();

        $redirector->shouldReceive('to')->zeroOrMoreTimes()
            ->andReturn($response);

        $generator->shouldReceive('previous')->zeroOrMoreTimes()
            ->andReturn('previous');

        return $redirector;
    }

    /**
     * Create a mock URL generator.
     *
     * @return \Illuminate\Routing\UrlGenerator
     */
    protected function createMockUrlGenerator()
    {
        return m::mock(UrlGenerator::class);
    }

    /**
     * Create a mock redirect response.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function createMockRedirectResponse()
    {
        return m::mock(RedirectResponse::class);
    }

}