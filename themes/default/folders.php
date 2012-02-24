<?php 
foreach ($this->folders as $f_id => $f_data){
    if (isset($f_data['path']) && isset($f_data['pattern'])){
	echo '<h2>'.$f_data['name'].'</h2>';
            
        try{
            echo '<ul class="folders">';
            $exclude = isset($f_data['exclude']) ? $f_data['exclude'] : null;
            $exclude_projects = isset($f_data['exclude_projects']) ? $f_data['exclude_projects'] : false;
            $dirs = new DirectoryIterator($f_data['path']);
            foreach ($dirs as $dir){
                $dirname = $dir->getBasename();
                if ( (!$exclude || !in_array($dirname, $exclude)) && $dir->isDir() && !$dir->isDot() && (!$exclude_projects || !isset($projects[$dirname]))){
                    echo '<li><a href="'.sprintf($f_data['pattern'],$dirname).'">'.$dirname.'</a></li>';
                }
            }
            echo '</ul>';
        }catch(Exception $e){
            
        }
    }
}
