<?php
class devHelper{
    protected $data;
    public function __construct($data){
        $this->data = $data;
    }
    public function __get($key){
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
}
