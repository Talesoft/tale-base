<?php

namespace Tale\Base;

use Tale\Base\Util\StringUtil;

class Controller
{

    const DEFAULT_CONTROLLER = 'index';
    const ERROR_CONTROLLER = 'error';
    const DEFAULT_ACTION = 'index';
    const DEFAULT_FORMAT = 'html';

    private $_data;

    public function __construct(array $data = null)
    {

        $this->_data = $data;
    }

    function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    function __get($name)
    {

        return $this->_data[$name];
    }

    function __set($name, $value)
    {

        $this->_data[$name] = $value;
    }

    function __unset($name)
    {
        unset($this->_data[$name]);
    }


    public static function getClassName($controller)
    {

        return StringUtil::camelize($controller).'Controller';
    }

    public static function getMethodName($action)
    {

        return StringUtil::variablize($action).'Action';
    }

    public static function getPublicMethodNames()
    {

        $ref = new \ReflectionClass(get_called_class());
        return array_keys(array_filter($ref->getMethods(\ReflectionMethod::IS_PUBLIC), function(\ReflectionMethod $method) {

            return !$method->isStatic();
        }));
    }

    public static function getInitMethodNames()
    {

        return array_filter(self::getPublicMethodNames(), function($methodName) {

            return strncmp($methodName, 'init', 4) !== 0;
        });
    }

    public static function dispatchError($action, array $request = null)
    {

        return self::dispatch([
            'controller' => self::ERROR_CONTROLLER,
            'action' => $action,
            'request' => $request ? $request : []
        ]);
    }

    public static function dispatch(array $request = null)
    {

        $request = array_replace([
            'controller' => self::DEFAULT_CONTROLLER,
            'action' => self::DEFAULT_ACTION,
            'args' => [],
            'format' => self::DEFAULT_FORMAT
        ], $request ? $request : []);

        //Sanitize (e.g. you can pass [sS]ome[-_][cC]ontroller, we want some-controller)
        $request['controller'] = StringUtil::canonicalize($request['controller']);
        $request['action'] = StringUtil::canonicalize($request['action']);
        $request['format'] = strtolower($request['format']);

        $className = self::getClassName($request['controller']);
        $methodName = self::getMethodName($request['action']);

        try {

            if (!class_exists($className) || !is_subclass_of($className, __CLASS__))
                throw new \Exception("Failed to dispatch controller: $className doesnt exist or is not an instance of ".__CLASS__);

            if (!method_exists($className, $methodName))
                throw new \Exception("Failed to dispatch action: $className has no method $methodName");

        } catch(\Exception $e) {

            return self::dispatchError('not-found', array_merge([
                'exception' => $e
            ], $request));
        }

        $controller = new $className($request);

        //Get controllers init* methods
        $initMethods = call_user_func([$className, 'getInitMethodNames']);

        //Call all init*-Methods
        $result = null;
        foreach ($initMethods as $method) {

            $result = call_user_func([$controller, $method]);

            if ($result)
                break;
        }

        //Call the action
        if (!$result)
            $result = call_user_func_array([$controller, $methodName], $request['args']);

        //Let the dev be able to change the format in the action
        $format = $controller->format;

        switch($format) {
            default:
            case 'html':

                header('Content-Type: text/html; encoding=utf-8');

                break;
            case 'json':

                header('Content-Type: application/json; encoding=utf-8');

                echo json_encode($result);
                break;
            case 'txt':

                header('Content-Type: text/plain; encoding=utf-8');

                echo serialize($result);
                break;
            case 'xml':

                header('Content-Type: text/xml; encoding=utf-8');

                $doc = new \DOMDocument('1.0', 'UTF-8');
                $doc->formatOutput = true;

                $addNode = function(\DOMNode $node, $name, $value, $self) use($doc) {

                    $type = gettype($value);

                    $el = $doc->createElement($name);
                    $typeAttr = $doc->createAttribute('type');
                    $typeAttr->value = $type;
                    $el->appendChild($typeAttr);
                    switch(strtolower($type)) {
                        case 'null':

                            $el->textContent = 'null';
                            break;
                        case 'string':
                        case 'int':
                        case 'double':

                            $el->textContent = (string)$value;
                            break;
                        case 'boolean':

                            $el->textContent = $value ? 'true' : 'false';
                            break;
                        case 'object':

                            if ($value instanceof Util\XmlSerializable)
                                $value = $value->xmlSerialize();
                            else
                                $value = (array)$value;
                        case 'array':

                            foreach ($value as $key => $val) {

                                $self($el, $key, $val, $self);
                            }
                            break;
                        case 'resource':

                            $el->textContent = get_resource_type($value);
                            break;
                    }
                };

                $addNode($doc, 'value', $result, $addNode);

                echo $doc->saveXML();
                break;
        }
    }
}