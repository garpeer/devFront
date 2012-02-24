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
 * @brief View class
 */
class devView{
    /**
     * @brief constructor
     */
    public function __construct(){
    }
    /**
     * @brief assign value to view
     * @param string $key
     * @param string $val
     * 
     * Values can be reached by $this->key in templates
     */
    public function assign($key, $val){
        $this->$key = $val;
    }
     /**
     * @brief assign value to view by reference
     * @param string $key
     * @param string $val
     * 
     * Values can be reached by $this->key in templates
     */
    public function assignRef($key, &$val){
        $this->$key =& $val;
    }
    /**
     * @brief fetch view
     * @param string $template template file with extension
     * @return string
     */
    public function fetch($template){
        $__simpleview_template = $template ;
        unset($template);
        unset($dir);
        ob_start();
        require($__simpleview_template);
        return (ob_get_clean());
        
    }
    /**
     * @brief display view
     * @param string $template template file with extension
     */
    public function display($template){
        echo $this->fetch($template); 
        
    }
}