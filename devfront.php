<?php
class devFront{
    protected $config;
    protected $locale;
    protected $servername;
    protected $url;
    protected $configfile = "config.php";
    protected $dir;
    protected $request;
    protected $projects;
    protected $folders;
    protected $notices = Array();
    public function __construct($url = 'http://localhost/devfront/'){
        try{
            require 'classes/view.php';
            require 'classes/locale.php';
            require 'classes/helper.php';
            $this->url = $url;
            $this->request = new devHelper($_REQUEST);
            $this->servername = $_SERVER['SERVER_NAME'] ? $_SERVER['SERVER_NAME'] : 'localhost';
            $this->configfile = $this->file($this->configfile);
            ob_start();
            if (!file_exists($this->configfile)){
                $this->install();
            }else{
                $config = file_get_contents($this->configfile);
                if ($config){
                    $config = unserialize($config);
                }
                if (!$config){
                    throw new Exception ('config file could not be read');
                }else{
                    $this->config = $config;
                }
            }
            $this->locale = new devLocale(@include $this->file('locale/'.$this->config['locale'].".php"));
            $this->projects = isset($this->config['projects']) ? $this->config['projects'] : null;              
            $this->folders = isset($this->config['folders']) ? $this->config['folders'] : null;  
            $page = $this->request->page ? $this->request->page.'_page' : 'index_page';
            if (method_exists($this, $page)){
                $this->$page();
            }else{
                header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
                echo '<h2>404 - Page not Found</h2>';
            }
            $content = ob_get_clean();
            $view = $this->get_view();
            $view->assign('title', $this->servername);
            $view->assign('content', $content);
            $view->assign('notices', $this->notices);
            $view->display($this->template('default.php'));
        }catch(Exception $e){
            echo 'Error: '. $e->getMessage();
        }

    }
    protected function index_page(){   
        if ($this->projects){
            $view = $this->get_view();
            $view->assign('projects',$this->projects);
            $view->display($this->template('projects.php'));
        }
        
        /*$folders = Array(
            'Folders'=>Array(
                'path'=>'.',
                'pattern'=>'http://localhost/%s/',
                'exclude' => Array('lister', 'NetBeansProjects','test','test.loc','bak','tmp','documentation','mypaint'),
                'exclude_projects' => true
            ),
            'Documentation'=>Array(
                'path'=>'./documentation/',
                'pattern'=>'/documentation/%s/html/'
            )
        );*/
        if ($this->folders){
            $view =  $this->get_view();
            $view->assign('folders',$this->folders);
            $view->display($this->template('folders.php'));
        }
    }
    protected function settings_page(){
        if ($this->request->type){
            $this->save_settings($this->request);
        }
        $view = $this->get_view();
        $dirs = new DirectoryIterator($this->file('themes'));
        $themes = Array();
        if ($dirs){
            foreach ($dirs as $dir){
                if (!$dir->isDot() && $dir->isDir()){
                    $themes[] = $dir->getFilename();
                }
            }
        }
        $dirs = new DirectoryIterator($this->file('locale'));
        $locales = Array();
        if ($dirs){
            foreach ($dirs as $dir){
                if ($dir->isFile()){
                    $locales[] = $dir->getBasename('.php');
                }
            }
        }
        $view->assign('themes',$themes);
        $view->assign('locales',$locales);
        $view->assign('projects',$this->projects);
        $view->assign('folders',$this->folders);
        $view->assign('request',$this->request);
        $view->assign('c_theme',$this->config['theme']);
        $view->assign('c_locale',$this->config['locale']);
        $view->display($this->template('settings.php'));
    }
    protected function save_settings(&$data){
        switch ($data->type){
            case 'basic': $this->save_settings_basic($data);
                break;
             case 'projects': $this->save_settings_projects($data);
                break;
            case 'folders': $this->save_settings_folders($data);
                break;
        }
    }
    protected function save_settings_basic(&$data){
        $theme = $data->theme;
        $locale = $data->locale;
        if ($theme){
            $this->config['theme'] = $data->theme;
        }
        if ($locale){
            $this->config['locale'] = $data->locale;
        }
        $this->save_config($this->config);
        $this->locale = new devLocale(@include $this->file('locale/'.$this->config['locale'].".php"));
    }
    protected function save_settings_folders(&$data){
        switch($data->action){
            case 'create':
                if ($data->name && $data->path && $data->pattern){
                    if (file_exists($data->path)){
                        $folder = Array(
                            'name' => $data->name,
                            'path'=> $data->path,
                            'pattern'=> $data->pattern
                        );
                        $this->config['folders'][] = $folder;
                        $data->delete('name');
                        $data->delete('path');
                        $data->delete('pattern');

                        $this->save_config($this->config);
                        $this->notify($this->locale->item_added);
                    }else{
                        $this->notify($this->locale->folder_does_not_exists,3);
                    }
                }
            break;
            case "update":
                if ($data->name && $data->path && $data->pattern){
                    if (file_exists($data->path)){
                        $folder = Array(
                            'name' => $data->name,
                            'path'=> $data->path,
                            'pattern'=> $data->pattern
                        );
                        $this->config['folders'][$data->id] = $folder;
                       $this->save_config($this->config);
                        $this->notify($this->locale->item_updated,1);
                    }else{
                        $this->notify($this->locale->folder_does_not_exists,3);
                    }
                }
                break;
            case "delete": 
                unset($this->config['folders'][$data->id]);
                $this->save_config($this->config);
                $this->notify($this->locale->item_deleted,2);
                break;
        }
    }
    protected function save_settings_projects(&$data){
        switch($data->action){
            case 'create':
                if ($data->name && $data->path){
                    $project = Array(
                        'name' => $data->name,
                        'path'=> $data->path,
                        'icon'=> $data->icon
                    );
                    $this->config['projects'][] = $project;
                    $data->delete('name');
                    $data->delete('path');
                    $data->delete('icon');
                }
                $this->save_config($this->config);
                $this->notify($this->locale->item_added);
            break;
            case "update":
                if ($data->name && $data->path){
                    $project = Array(
                        'name' => $data->name,
                        'path'=> $data->path,
                        'icon'=> $data->icon
                    );
                    $this->config['projects'][$data->id] = $project;
                }
                $this->save_config($this->config);
                $this->notify($this->locale->item_updated,1);
                break;
            case "delete": 
                unset($this->config['projects'][$data->id]);
                $this->save_config($this->config);
                $this->notify($this->locale->item_deleted, 2);
                break;
        }
    }
    protected function install(){
        $this->save_config(Array('theme'=>'default','locale' =>'en'));
        throw new Exception ('stug');
    }
    protected function file($file=false){
        if (!isset($this->dir)){
            $dir = dirname(__FILE__);
            $this->dir = $dir."/";
        }
       return $this->dir. $file;
    }
    protected function template($file){
        return $this->file("themes/".$this->config['theme'] ."/". $file);
    }
    protected function save_config($config){
        if (!file_put_contents($this->configfile, serialize($config))){
            throw new Exception ('failed to save config data to'. $this->file($this->configfile));
        }else{            
            $this->projects = $config['projects'];
            $this->folders = $config['folders'];
        }
    }
    protected function get_view(){
        $view = new devView();
        $view->assign('theme_dir',$this->url. "themes/".$this->config['theme'] ."/");
        $view->assignRef('locale',$this->locale);
        return $view;
    }
    protected function notify($msg, $level = 0){
        $this->notices[] = Array('message' => $msg, 'level'=> $level);
    }
}
