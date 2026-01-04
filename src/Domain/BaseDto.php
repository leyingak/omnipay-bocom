<?php

namespace Omnipay\Bocom\Domain;

use BadMethodCallException;
use Omnipay\Bocom\Util\StrUtil;

abstract class BaseDto
{

    protected $data = [];

    public function __construct($data = [])
    {
        if ($data) {
            $this->fromArray($data);
        }
    }

    abstract protected function schema();

    public function fromArray(array $arr)
    {
        $schema = $this->schema();

        foreach ($schema as $field => $rule) {
            if (!array_key_exists($field, $arr)) continue;
            $value = $arr[$field];

            if (is_array($rule) && isset($rule['class'])) {
                $cls = $rule['class'];

                if (!empty($rule['list'])) {
                    $list = array();
                    if (is_array($value)) {
                        foreach ($value as $item) {
                            $list[] = ($item instanceof $cls) ? $item : new $cls((array)$item);
                        }
                    }
                    $this->data[$field] = $list;
                } else {
                    $this->data[$field] = ($value instanceof $cls) ? $value : new $cls((array)$value);
                }
                continue;
            }

            $this->data[$field] = $value;
        }

        return $this;
    }

    public function toArray()
    {
        $schema = $this->schema();
        $out    = array();

        foreach ($schema as $field => $rule) {
            if (!array_key_exists($field, $this->data)) continue;
            $val = $this->data[$field];

            if (is_array($rule) && isset($rule['class'])) {
                if (!empty($rule['list'])) {
                    $tmp = array();
                    foreach ((array)$val as $obj) {
                        $tmp[] = ($obj instanceof BaseDto) ? $obj->toArray() : $obj;
                    }
                    $out[$field] = $tmp;
                } else {
                    $out[$field] = ($val instanceof BaseDto) ? $val->toArray() : $val;
                }
            } else {
                $out[$field] = $val;
            }
        }

        return $out;
    }

    public function get($field, $default = null)
    {
        return array_key_exists($field, $this->data) ? $this->data[$field] : $default;
    }

    public function set($field, $value)
    {
        $this->data[$field] = $value;
        return $this;
    }

    public function __call($name, $args)
    {
        if (strpos($name, 'get') === 0) {
            $prop = substr($name, 3); // RspBizContent
            if ($prop === '') throw new BadMethodCallException("Invalid getter $name");
            $field = StrUtil::camelToSnake($prop);
            return $this->get($field);
        }

        if (strpos($name, 'set') === 0) {
            $prop = substr($name, 3);
            if ($prop === '') throw new BadMethodCallException("Invalid setter $name");
            $field = StrUtil::camelToSnake($prop);
            $this->set($field, isset($args[0]) ? $args[0] : null);
            return $this;
        }

        throw new BadMethodCallException("Method $name not found");
    }


}