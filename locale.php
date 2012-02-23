<?php
class devLocale{
    protected $__strings;
    public function __construct($strings){
        $this->__strings = $strings;
    }
    public function __get($key){
        return isset($this->__strings[$key]) ? $this->__strings[$key] : $key;
    }
}
