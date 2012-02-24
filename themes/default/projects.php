<?php
if ($this->projects){
    echo '<h2>'. $this->locale->projects. '</h2>
    <ul class="projects">';
    foreach ($this->projects as $id => $name){  
        $style = $name['icon'] ? 'style="background-image: url(/lister/images/glossy.png), url(/lister/images/'. $name['icon'] .');"' : '';
        echo '<li><a href="'.$name['path'].'" '.$style.'>'.$name['name'].'</a></li>';
    }
    echo '</ul>';
}
