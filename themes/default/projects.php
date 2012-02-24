<?php
if ($this->projects){
    echo '<h2>'. $this->locale->projects. '</h2>
    <ul class="projects">';
    foreach ($this->projects as $id => $name){  
        $style = $name['icon'] ? 'style="background-image: url('. $name['icon'] .');"' : '';
        echo '<li '.$style.'><a href="'.$name['path'].'">'.$name['name'].'</a></li>';
    }
    echo '</ul>';
}
