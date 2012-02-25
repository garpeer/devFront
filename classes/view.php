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
    public function assign( $key, $val ){
        $this->$key = $val;
    }
     /**
     * @brief assign value to view by reference
     * @param string $key
     * @param string $val
     * 
     * Values can be reached by $this->key in templates
     */
    public function assignRef( $key, &$val ){
        $this->$key =& $val;
    }
    /**
     * @brief fetch view
     * @param string $template template file with extension
     * @return string
     */
    public function fetch( $template ){
        $this->devView_template = $template ;
        unset( $template );
        unset( $dir );
        ob_start();
        require( $this->template_hider() );
        return( ob_get_clean() );
        
    }
    protected function template_hider(){
        $template = $this->devView_template;
        unset( $this->devView_template );
        return $template;
    }
    /**
     * @brief display view
     * @param string $template template file with extension
     */
    public function display( $template ){
        echo $this->fetch( $template ); 
        
    }
    /**
     * @brief clean string
     * @param string $str
     * @return string 
     */
    public function clean( $str ){
        return htmlspecialchars( html_entity_decode( $str, ENT_QUOTES, 'UTF-8' ), ENT_QUOTES, 'UTF-8' );
    }
}