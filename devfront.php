<?php
class devFront{
    protected $config;
    protected $locale;
    protected $servername;
    protected $configfile = "config.php";
    protected $dir;
    public function __construct(){
        try{
            require 'view.php';
            require 'locale.php';
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
            $page = isset($_GET['page']) ? $_GET['page'].'_page' : 'index_page';
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
        $projects = isset($this->config['projects']) ? $this->config['projects'] : null;  
        if ($projects){
            $view = $this->get_view();
            $view->assign('projects',$projects);
            $view->display($this->template('projects.php'));
        }
        
        $folders = isset($this->config['folders']) ? $this->config['folders'] : null;  
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
        if ($folders){
            $view =  $this->get_view();
            $view->assign('folders',$folders);
            $view->display($this->template('folders.php'));
        }
    }
    protected function settings_page(){
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
        $view->assign('c_theme',$this->config['theme']);
        $view->assign('c_locale',$this->config['locale']);
        $view->display($this->template('settings.php'));
    }
    protected function install(){
        $this->save_config(Array('theme'=>'default','locale' =>'en'));
        throw new Exception ('stug');
    }
    protected function file($file){
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
        }
    }
    protected function get_view(){
        $view = new devView();
        $view->assignRef('locale',$this->locale);
        return $view;
    }
}
