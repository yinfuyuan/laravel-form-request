## [README Of English](https://github.com/yinfuyuan/laravel-form-request)

## 概述

在开发的过程中，经常会遇到表单验证的需求，Laravel为我们提供了功能强大且可拓展的验证器使我们可以快速实现此类需求，但是由于Laravel并没有为我们提供类似于 [yii2的场景](https://www.yiiframework.com/doc/guide/2.0/zh-cn/structure-models#scenarios) 的概念，这使得我们要为每一个
带有验证规则的请求都创建一个请求类文件，或将验证规则直接写在控制器中。这可能听起来没什么问题，但在实际开发中我们遇到非常复杂的业务场景，需要提供成百上千个接口，也就是说要创建成百上千个请求类文件或把成百上千个验证规则都写在控制器中。

为了解决这个问题，参考了 [yii2的场景](https://www.yiiframework.com/doc/guide/2.0/zh-cn/structure-models#scenarios) ，尝试在Laravel中实现场景的概念，但是Laravel有很大的不同，在Laravel中自动验证的Requst类是注入进来的，这样想实现自动验证的时候就已经使用了正确的场景除非是通过表单提交的时候携带场景参数或者尝试使用Middleware进行处理，但这两种方式都不是太理想，经过一段时间的尝试和思考，想到可以利用变量名来传递场景信息。

于是开始实现，本想着很好解决，通过内置函数或者魔术函数或者反射类能轻松在类内部获取到这个类被实例化的名称，结果在这里浪费了很长很长时间，尝试了很多方式，但是依然没有办法获取实例名称。最后已经快放弃的时候。试到了可以通过Laravel路由来获取要到达的控制器类，再通过反射类来取到变量名称。

## 安装

    composer require laravel-form-request/laravel-form-request
    
## 文档

### 表单请求基类 [源码](https://github.com/yinfuyuan/laravel-form-request/blob/master/src/FormRequest.php) [测试用例](https://github.com/yinfuyuan/laravel-form-request/blob/master/tests/FormRequestTest.php)

表单请求基类是抽象类，不能直接实例化使用，所有需要用到场景的请求类都需要继承该类。

表单请求基类继承自Laravel的 表单请求类 其中下面方法使用final关键字修饰，不能进行重写，需要使用场景名称+方法名称驼峰写法进行重写以实现不同场景使用不同的验证规则。
* authorize()
* rules()
* messages()
* attributes()

表单请求基类为上述方法使用默认场景提供了下面的重写方法，需要继承后重写自己的规则。
* defaultAuthorize()
* defaultRules()
* defaultMessages()
* defaultAttributes()

表单请求基类默认的场景是default，可以在子类中使用 const SCENARIO_DEFAULT = 'login' 修改默认场景名称，表单请求基类提供了以下方法获取和重新设置场景
* getScenario()
* setScenario(string $scenario)

表单请求基类重写了Laravel表单请求类的prepareForValidation方法以实现截取请求类的变量名称用作场景，prepareForValidation方法也使用final关键字修饰，不能进行重写。其中的主要功能都通过Laravel的路由来实现，所以如果请求不是通过路由构建的，无法使用此功能。
表单请求基类默认使用Request对变量进行截取，如$defaultRequest截取的场景名称为default,可以在子类中使用 protected $delimiter = '_request'; 修改默认分隔符。

下面以用户的表单请求类为例，介绍场景的使用，如下定义中定义了三个场景，分别是default index closure

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

然后在控制器中用同一个请求类进行定义，在请求到达控制器之前会根据变量名称转化成不同的场景规则进行验证

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
    
也可以不使用控制器，直接使用路由闭包的形式进行验证

    Route::get('users', function (UserFormRequest $closureRequest) {
        echo $closureRequest->getScenario(); // closure
    });