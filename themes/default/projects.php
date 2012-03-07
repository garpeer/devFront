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
if ($this->projects){
    echo '<h2>'. $this->locale->projects. '</h2>
    <ul class="projects">';
    foreach ($this->projects as $id => $name){  
        $style = $name['icon'] ? 'style="background-image: url('. $this->clean( $name['icon'] ) .');"' : '';
        echo '<li '.$style.'><a href="'.$this->clean( $name['formatted_path'] ).'">'.$this->clean( $name['name'] ).'</a></li>';
    }
    echo '</ul>';
}
