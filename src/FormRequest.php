<?php

namespace LaravelFormRequest;

/**
 * Class FormRequest
 *
 * @author yinfuyuan <yinfuyuan@gmail.com>
 * @link https://github.com/yinfuyuan/laravel-form-request
 */
abstract class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{

    /**
     * The default scenario.
     *
     * @var string
     */
    const SCENARIO_DEFAULT = 'default';

    /**
     * The scenario.
     *
     * @var string $scenario
     */
    private $scenario = self::SCENARIO_DEFAULT;

    /**
     * The delimiter used to get the scenario.
     *
     * @var string $delimiter
     */
    protected $delimiter = 'Request';

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public final function prepareForValidation()
    {

        $route = $this->route();

        if(!empty($route) && is_string($route->action['uses'])) {
            $controllerAction = explode('@', $route->getActionName());
            $controller = reset($controllerAction);
            $action = end($controllerAction);
            if(!class_exists($controller)) {
                return;
            }
            $reflectionClass = new \ReflectionClass($controller);
            if(!$reflectionClass->hasMethod($action)) {
                return;
            }
            $parameters = $reflectionClass->getMethod($action)->getParameters();
        } elseif (!empty($route) && $route->getActionName() == 'Closure') {
            $reflectionFunction = new \ReflectionFunction($this->route()->action['uses']);
            $parameters = $reflectionFunction->getParameters();
        } else {
            return;
        }

        foreach ($parameters as $parameter) {
            if($parameter->getClass() == null || $parameter->getClass()->getName() != static::class) {
                continue;
            }
            if(empty($this->delimiter)) {
                $this->setScenario($parameter->getName());
                break;
            }
            $requestName = explode($this->delimiter, $parameter->getName());
            if(!empty($requestName[0])) {
                $this->setScenario($requestName[0]);
            }
            break;
        }

    }

    /**
     * Get custom authorize for validator errors.
     *
     * @return array
     */
    public final function authorize()
    {
        return $this->callScenarioMethod('Authorize');
    }

    /**
     * Get custom rules for validator errors.
     *
     * @return array
     */
    public final function rules()
    {
        return $this->callScenarioMethod('Rules');
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public final function messages()
    {
        return $this->callScenarioMethod('Messages');
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public final function attributes()
    {
        return $this->callScenarioMethod('Attributes');
    }

    /**
     * Set the scenario.
     *
     * @param $scenario
     */
    public function setScenario(string $scenario)
    {
        $this->scenario = $scenario;
    }

    /**
     * Get the scenario.
     *
     * @return string
     */
    public function getScenario()
    {
        return $this->scenario;
    }

    /**
     * Get the default authorize.
     *
     * @return bool
     */
    protected function defaultAuthorize()
    {
        return true;
    }

    /**
     * Get the default rules.
     *
     * @return array
     */
    protected function defaultRules()
    {
        return [];
    }

    /**
     * Get the default messages.
     *
     * @return array
     */
    protected function defaultMessages()
    {
        return parent::messages();
    }

    /**
     * Get the default attributes.
     *
     * @return array
     */
    protected function defaultAttributes()
    {
        return parent::attributes();
    }

    /**
     * Call the scenario method.
     *
     * @param $method
     * @return mixed
     */
    private function callScenarioMethod($method)
    {
        $scenarioMethod = $this->scenario . $method;
        if(!method_exists($this, $scenarioMethod)) {
            $scenarioMethod = self::SCENARIO_DEFAULT . $method;
        }
        return $this->{$scenarioMethod}();
    }

}