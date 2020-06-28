## [中文文档](https://github.com/yinfuyuan/laravel-form-request/blob/master/README_CN.md)

## Overview

In the process of develop, often will meet the demand of the form validation, Laravel provides us with powerful and can expand the validator that we can quickly finish this kind of demand, but because the Laravel did not provide us with similar to [yii2 scenario](https://www.yiiframework.com/doc/guide/2.0/zh-cn/structure-models#scenarios) concept, which we will make each one requests with validation rules create a request class file or write the validation rules directly to the controller. This may not sound like a problem, but in real develop we have a very complex business scenario where we need to provide thousands of interfaces, which means creating thousands of request class files or writing thousands of validation rules in the controller.

In order to solve this problem, refer [yii2 scene](https://www.yiiframework.com/doc/guide/2.0/zh-cn/structure-models#scenarios), try to implementation scenario in Laravel, but Laravel is very different, automatic validation requst class is injected in Laravel controller, so if you want to do that unless you use form parameters or tried to use Middleware, but these two options were not ideal. After some time of trying and thinking, I came up with the idea of using parameter name to pass the scene information.

So I started to implement, thinking that it would be easy to get the instantiated name of the class inside the class through built-in functions or magic functions or reflection classes, but I wasted a lot of time and tried many ways, but still couldn't get the instance name. When I was about to give up. Try to get the controller class to arrive through the Laravel route, and get the variable name through the reflection class.

## Install

    composer require laravel-form-request/laravel-form-request
    
## Document

* ### FormRequest [src](https://github.com/yinfuyuan/laravel-form-request/blob/master/src/FormRequest.php) [tests](https://github.com/yinfuyuan/laravel-form-request/blob/master/tests/FormRequestTest.php)

The form request base class is abstract and cannot be instantiated directly. All request classes that need to use the scenario need to inherit from it.

The form request base class is inherited from Laravel's form request class. The following method is modified with final keyword and cannot be overridden. It needs to be overridden with scenario name + method name camel case to implement different validation rules for different scenarios.
* authorize()
* rules()
* messages()
* attributes()

The form request base class provides the following override method for the above method using the default scenario, and needs to override its own rules after inheritance.
* defaultAuthorize()
* defaultRules()
* defaultMessages()
* defaultAttributes()

The default scenario of the form request base class is Default, and the default scenario name can be modified using const SCENARIO_DEFAULT = 'login' in the subclasses. The form request base class provides the following methods to retrieve and reset the scenario
* getScenario()
* setScenario(string $scenario)

The form request base class overrides the prepareForValidation method of the Laravel form request class to capture the variable name of the request class as the scenario, and the prepareForValidation method is also decorated with the final keyword and cannot be overridden. The main features are implemented via Laravel route, so this feature cannot be used if the request is not built via route.
Form Request base class intercepts variables using Request by default. For example, the scenario name of defaultRequest intercepts is default. You can use protected $delimiter = '_request' in subclasses. Change the default delimiter.

The following scenario USES a user's form request class as an example. Three scenarios, default index closure, are defined in the following definition

    use LaravelFormRequest\FormRequest;
    class UserFormRequest extends FormRequest
    {
        public function defaultRules()
        {
            return [];
        }
        public function defaultMessages()
        {
            return [];
        }
        public function indexRules()
        {
            return [
                'name' => 'required',
            ];
        }
        public function indexMessages()
        {
            return [
                'name.required' => 'The name field is required in index scenario.',
            ];
        }
        public function closureRules()
        {
            return [
                'name' => 'required',
            ];
        }
        public function closureMessages()
        {
            return [
                'name.required' => 'The name field is required in closure scenario.',
            ];
        }
    }

The same request class is then defined in the controller, and the request is validated against a different scenario rule based on the variable name before it reaches the controller

    use Illuminate\Routing\Controller as BaseController;
    class UserController extends BaseController
    {
        public function index(UserFormRequest $request)
        {
            echo $request->getScenario(); // default
        }
        public function index(UserFormRequest $indexRequest)
        {
            echo $indexRequest->getScenario(); // index
        }
    }
    
You can also use routing closures for validation without using a controller

    Route::get('users', function (UserFormRequest $closureRequest) {
        echo $closureRequest->getScenario(); // closure
    });