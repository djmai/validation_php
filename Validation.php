<?php

/**
 * Validation Class
 *
 * Clase simple de PHP para validación.
 *
 * @author Miguel Martínez <miguel.martinez88@outlook.es>
 * @copyright (c) 2018, Miguel Martínez
 * @license https://github.com/djmai/validation_php/blob/main/LICENSE
 * @link https://github.com/djmai/validation_php
 */

class Validation
{

    /**
     * @return array $patterns
     */
    public $patterns = array(
        'uri'           => '[A-Za-z0-9-\/_?&=]+',
        'url'           => '[A-Za-z0-9-:.\/_?&=#]+',
        'alpha'         => '[\p{L}]+',
        'words'         => '[\p{L}\s]+',
        'alphanum'      => '[\p{L}0-9]+',
        'int'           => '[0-9]+',
        'float'         => '[0-9\.,]+',
        'tel'           => '[0-9+\s()-]+',
        'text'          => '[\p{L}0-9\s-.,;:!"%&()?+\'°#\/@]+',
        'file'          => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+\.[A-Za-z0-9]{2,4}',
        'folder'        => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+',
        'address'       => '[\p{L}0-9\s.,()°-]+',
        'date_dmy'      => '[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{4}',
        'date_ymd'      => '[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}',
        'email'         => '[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+[.]+[a-z-A-Z]'
    );

    /**
     * @return array $errors
     */
    public $errors = array();

    /**
     * @param string $name
     * @return this
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed $value
     * @return this
     */
    public function value($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param mixed $value
     * @return this
     */
    public function file($value)
    {
        $this->file = $value;
        return $this;
    }

    /**
     * @param string $name nome del pattern
     * @return this
     */
    public function pattern($name)
    {
        if ($name == 'array') {
            if (!is_array($this->value)) {
                $this->errors[] = 'Formato de campo ' . $this->name . ' no valido.';
            }
        } else {
            $regex = '/^(' . $this->patterns[$name] . ')$/u';
            if ($this->value != '' && !preg_match($regex, $this->value)) {
                $this->errors[] = 'Formato de campo ' . $this->name . ' no valido.';
            }
        }
        return $this;
    }

    /**
     * @param string $pattern
     * @return this
     */
    public function customPattern($pattern)
    {
        $regex = '/^(' . $pattern . ')$/u';
        if ($this->value != '' && !preg_match($regex, $this->value)) {
            $this->errors[] = 'Formato de campo ' . $this->name . ' no valido.';
        }
        return $this;
    }

    /**
     * @return this
     */
    public function required()
    {
        if ((isset($this->file) && $this->file['error'] == 4) || ($this->value == '' || $this->value == null)) {
            $this->errors[] = 'El campo ' . $this->name . ' es obligatorio.';
        }
        return $this;
    }

    /**
     * @param int $min
     * @return this
     */
    public function min($length)
    {
        if (is_string($this->value)) {
            if (strlen($this->value) < $length) {
                $this->errors[] = 'El valor del campo ' . $this->name . ' es menor que el valor mínimo';
            }
        } else {
            if ($this->value < $length) {
                $this->errors[] = 'El valor del campo ' . $this->name . ' es menor que el valor mínimo';
            }
        }
        return $this;
    }

    /**
     * @param int $max
     * @return this
     */
    public function max($length)
    {
        if (is_string($this->value)) {
            if (strlen($this->value) > $length) {
                $this->errors[] = 'El valor del campo ' . $this->name . ' es superior al valor máximo';
            }
        } else {
            if ($this->value > $length) {
                $this->errors[] = 'El valor del campo ' . $this->name . ' es superior al valor máximo';
            }
        }
        return $this;
    }

    /**
     * @param mixed $value
     * @return this
     */
    public function equal($value)
    {
        if ($this->value != $value) {
            $this->errors[] = 'El valor del campo ' . $this->name . ' no es igual.';
        }
        return $this;
    }

    /**
     * @param int $size
     * @return this 
     */
    public function maxSize($size)
    {
        if ($this->file['error'] != 4 && $this->file['size'] > $size) {
            $this->errors[] = 'El archivo ' . $this->name . ' excede el tamaño máximo de ' . number_format($size / 1048576, 2) . ' MB.';
        }
        return $this;
    }

    /**
     * @param string $extension
     * @return this 
     */
    public function ext($extension)
    {
        if ($this->file['error'] != 4 && pathinfo($this->file['name'], PATHINFO_EXTENSION) != $extension && strtoupper(pathinfo($this->file['name'], PATHINFO_EXTENSION)) != $extension) {
            $this->errors[] = 'El archivo ' . $this->name . ' no es ' . $extension . '.';
        }
        return $this;
    }

    /**
     * @param string $string
     * @return $string
     */
    public function purify($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        if (empty($this->errors)) return true;
    }

    /**
     * @return array $this->errors
     */
    public function getErrors()
    {
        if (!$this->isSuccess()) return $this->errors;
    }

    /**
     * @return string $html
     */
    public function displayErrors()
    {

        $html = '<ul>';
        foreach ($this->getErrors() as $error) {
            $html .= '<li>' . $error . '</li>';
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     * @return booelan|string
     */
    public function result()
    {

        if (!$this->isSuccess()) {

            foreach ($this->getErrors() as $error) {
                echo "$error\n";
            }
            exit;
        } else {
            return true;
        }
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public static function is_int($value)
    {
        if (filter_var($value, FILTER_VALIDATE_INT)) return true;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public static function is_float($value)
    {
        if (filter_var($value, FILTER_VALIDATE_FLOAT)) return true;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public static function is_alpha($value)
    {
        if (filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z]+$/")))) return true;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public static function is_alphanum($value)
    {
        if (filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z0-9]+$/")))) return true;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public static function is_url($value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) return true;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public static function is_uri($value)
    {
        if (filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[A-Za-z0-9-\/_]+$/")))) return true;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public static function is_bool($value)
    {
        if (is_bool(filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE))) return true;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public static function is_email($value)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) return true;
    }
}
