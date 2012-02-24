<?php 
foreach ($this->folders as $folder){
    echo '<h2>'.$folder['name'].'</h2>';
    if ($folder['dirs']){
        echo '<ul class="folders">';    
        foreach ($folder['dirs'] as $dir){
            echo '<li><a href="'.sprintf($folder['pattern'],$dir).'">'.$dir.'</a></li>';
        }
        echo '</ul>';
    }else{
        echo '<p class="no-item">'.$this->locale->folder_does_not_exists . "</p>";
    }
}
