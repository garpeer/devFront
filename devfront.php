<?php
class devFront{
    protected $config;
    protected $locale;
    protected $servername;
    protected $url;
    protected $configfile = "config.php";
    protected $dir;
    protected $get;
    protected $post;
    protected $projects;
    protected $folders;
    public function __construct($url = 'http://localhost/devfront/'){
        try{
            require 'view.php';
            require 'locale.php';
            require 'helper.php';
            $this->url = $url;
            $this->post = new devHelper($_POST);
            $this->get = new devHelper($_GET);
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
            $page = $this->get->page ? $this->get->page.'_page' : 'index_page';
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
        if ($this->post->type){
            $this->save_settings($this->post);
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
        $view->assign('data',$this->post);
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
            case 'folders': $this->save_settings_folders($data->folders);
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
    protected function save_settings_projects(&$data){
        var_dump($data);
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
            break;
        }
        echo "OK";
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
    protected function theme_dir(){
        
    }
    protected function template($file){
        return $this->file("themes/".$this->config['theme'] ."/". $file);
    }
    protected function save_config($config){
        if (!file_put_contents($this->configfile, serialize($config))){
            throw new Exception ('failed to save config data to'. $this->file($this->configfile));
        }
    }
    protected function get_view(){
        $view = new devView();
        $view->assign('theme_dir',$this->url. "themes/".$this->config['theme'] ."/");
        $view->assignRef('locale',$this->locale);
        return $view;
    }
}
