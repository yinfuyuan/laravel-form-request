<?php

abstract class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{

    const SCENARIO_DEFAULT = 'default';

    protected $scenario = self::SCENARIO_DEFAULT;

    public function prepareForValidation()
    {

        $router = $this->route();

        $isControllerAction = is_string($router->action['uses']) ? true : false;

        if($isControllerAction) {
            $controllerAction = explode('@', $router->getActionName());
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
        } elseif ($router->getActionName() == 'Closure') {
            $reflectionFunction = new \ReflectionFunction($this->route()->action['uses']);
            $parameters = $reflectionFunction->getParameters();
        } else {
            return;
        }

        foreach ($parameters as $parameter) {
            if($parameter->getClass() == null || $parameter->getClass()->getName() != static::class) {
                continue;
            }
            $requestName = explode('request', $parameter->getName());
            if(!empty($requestName[0])) {
                $this->setScenario($requestName[0]);
            }
            break;
        }

    }

    public function authorize()
    {
        $scenarioMethod = $this->scenario . 'Authorize';
        if(!method_exists($this, $scenarioMethod)) {
            $this->scenario = self::SCENARIO_DEFAULT;
            $scenarioMethod = $this->scenario . 'Authorize';
        }
        return $this->{$scenarioMethod}();
    }

    public function rules()
    {
        $scenarioMethod = $this->scenario . 'Rules';
        if(!method_exists($this, $scenarioMethod)) {
            $this->scenario = self::SCENARIO_DEFAULT;
            $scenarioMethod = $this->scenario . 'Rules';
        }
        return $this->{$scenarioMethod}();
    }

    public function messages()
    {
        $scenarioMethod = $this->scenario . 'Messages';
        if(!method_exists($this, $scenarioMethod)) {
            $this->scenario = self::SCENARIO_DEFAULT;
            $scenarioMethod = $this->scenario . 'Messages';
        }
        return $this->{$scenarioMethod}();
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

}