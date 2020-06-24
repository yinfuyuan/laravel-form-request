<?php

namespace LaravelFormRequest\Tests;

use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\Router;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class FormRequestTest extends TestCase
{

    /**
     * @var \Illuminate\Routing\Router
     */
    protected $router;


    protected function setUp(): void
    {
        parent::setUp();

        $this->router = new Router(m::mock(Dispatcher::class), Container::getInstance());

        $this->router->resource('users', UserController::class);

        $this->router->get('user', function (UserFormRequest $closureRequest) {
            $closureRequest->validateResolved();
            return $closureRequest->all();
        });
    }

    protected function tearDown(): void
    {
        m::close();
    }

    public function testControllerAction()
    {
        // todo
    }

    public function testClosure()
    {
        // todo
    }

}