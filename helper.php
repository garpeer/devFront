<?php
class devHelper{
    protected $data;
    public function __construct($data){
        $this->data = $data;
    }
    public function __get($key){
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
    public function __set($key, $value){
        $this->data[$key] = $value;
    }
    public function delete($key){
        unset($this->data[$key]);
    }
}
