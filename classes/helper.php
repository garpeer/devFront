<?php
/*
 * devFront localhost frontend
 * Copyright (C) 2012 Gergely Aradszki (garpeer)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
 */
/**
 * @brief helper object
 */
class devHelper{
    /**
     * @brief objects data
     * @var array 
     */
    protected $data;
    /**
     * @brief constructor
     * @param array $data initial data
     */
    public function __construct($data){
        $this->data = $data;
    }
    /**
     * @brief getter
     * @param String $key
     * @return mixed 
     */
    public function __get($key){
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
    /**
     * @brief setter
     * @param string $key
     * @param mixed $value 
     */
    public function __set($key, $value){
        $this->data[$key] = $value;
    }
    /**
     * @brief delete data
     * @param string $key 
     */
    public function delete($key){
        unset($this->data[$key]);
    }
}
