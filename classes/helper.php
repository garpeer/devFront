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
     * @brief constructor
     * @param array $data initial data
     */
    public function __construct($data = array()){
        if ($data){
            foreach ($data as $key => $value){
                $this->$key = $value;
            }
        }
    }
    /**
     * @brief getter
     * @param String $key
     * @return mixed 
     */
    public function __get($key){
        return null;
    }
}
