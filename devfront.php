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
 * @brief core class
 */
class devFront {
    /**
     * @brief configuration
     * @var devHelper
     */
    protected $config;
    /**
     * @brief locale object
     * @var devLocale
     */
    protected $locale;
    /**
     * @brief server name (eg. localhost)
     * @var string
     */
    protected $servername;
    /**
     * @brief devfront url for images & css
     * @var string
     */
    protected $url;
    /**
     * @brief configuration file
     * @var string
     */
    protected $configfile = "config.php";
    /**
     * @brief app dir
     * @var string
     */
    protected $dir;
    /**
     * @brief request object
     * @var devHelper
     */
    protected $request;
    /**
     * @brief projects
     * @var array
     */
    protected $projects;
     /**
     * @brief folders
     * @var array
     */
    protected $folders;
     /**
     * @brief notices
     * @var array
     */
    protected $notices = Array();

     /**
      * @brief constructor
      * @param string $url devfront url for images & css
      */
    public function __construct($url = false) {
        try {
            require 'classes/view.php';
            require 'classes/locale.php';
            require 'classes/helper.php';
            $this->servername = $_SERVER['SERVER_NAME'] ? $_SERVER['SERVER_NAME'] : 'localhost';
            $this->url = $url ? $url : '/devfront/';
            $this->configfile = $this->file($this->configfile);	    
            ob_start();
            if (!file_exists($this->configfile)) {
                $this->install();
            }

            $config = file_get_contents($this->configfile);
            if ($config) {
                $config = unserialize($config);
                if (is_object($config)){
                    $this->config = $config;
                }
                if (is_array($config)){
                    $this->config = new devHelper($config);
                }
            }
            if (!$this->config) {
                throw new Exception('config file could not be read');
            } else {
                $this->config->allow_ip = isset($this->config->allow_ip) ? $this->config->allow_ip : Array();
            }
            $_REQUEST['is_admin'] = $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1' || in_array($_SERVER['REMOTE_ADDR'], $this->config->allow_ip);
            $this->request = new devHelper($_REQUEST);
            $this->locale = new devLocale(@include $this->file('locale/' . $this->config->locale . ".php"));
            $this->projects = $this->formatProjects($this->config->projects);      
           
            $this->folders = $this->formatFolders($this->config->folders);            
            $page = $this->request->page ? $this->request->page . '_page' : 'index_page';
            if (method_exists($this, $page)) {
                $this->$page();
            } else {
                header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
                echo '<h2>404 - Page not Found</h2>';
            }
            $content = ob_get_clean();

	    $home = explode('?',$_SERVER["REQUEST_URI"]);
	    $home = $home[0];
            $view = $this->get_view();
	    $view->assign('home',$home);
            $view->assign('title', $this->servername);
            $view->assign('content', $content);
            $view->assign('notices', $this->notices);
            $view->display($this->template('default.php'));
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
    protected function formatProjects($projects){
         if ($projects){
            foreach ($projects as &$project) {
                if (!isset($project['path'])) {
                    $project['path'] = "";
                    $project['formatted_path'] = "";
                }else{
                    $project['formatted_path'] = str_replace('%HOST%', 'http://'.$this->servername, $project['path']);
                }
                if (!isset($project['icon'])) {
                    $project['icon'] = false;
                }
                if (!isset($project['active']) || $project['active'] == true){                        
                    $project['active'] = true;
                }else{
                    $project['active'] = false;
                }
            }
        }
        return $projects;
    }
    protected function formatFolders($folders){
        if ($folders){
            foreach ($folders as &$folder){
                $folder['formatted_pattern'] = isset($folder['pattern']) ? str_replace('%HOST%', 'http://'.$this->servername, $folder['pattern']) : '';
            }
        }
        return $folders;
    }
    /**
     * @brief index page
     */
    protected function index_page() {
        if ($this->projects) {
            $view = $this->get_view();
            $projects = array();
            foreach ($this->projects as $project){
                if ($project['active']){
                    $project['icon'] = $project['icon'] ? $this->url . "project_images/".$project['icon']: false;
                    $projects[] = $project;
                }                
            }            
            $view->assign('projects', $projects);
            $view->display($this->template('projects.php'));
        }
        if ($this->folders) {

            foreach ($this->folders as &$folder) {
                $exclude = isset($folder['exclude']) ? $folder['exclude'] : null;
                if (file_exists($folder['path'])){
                    $dirs = new DirectoryIterator($folder['path']);
                    foreach ($dirs as $dir){
                        $dirname = $dir->getBasename();
                        if ( (!$exclude || !in_array($dirname, $exclude)) && $dir->isDir() && !$dir->isDot() ){
                            $folder['dirs'][$dirname] = str_replace('%FOLDER%', $dirname, $folder['formatted_pattern']);
                        }
                    }
                }else{
                    $folder['dirs'] = false;
                }
            }
            $view = $this->get_view();
            $view->assign('folders', $this->folders);
            $view->display($this->template('folders.php'));
        }
    }
    /**
     * @brief settings page
     */
    protected function settings_page() {
        
        if (!$this->request->is_admin){
            header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
            echo '<h2>403 - Forbidden</h2>';
            return;
        }
        if ($this->request->type) {
            $this->save_settings($this->request);
        }
        $view = $this->get_view();
        $dirs = new DirectoryIterator($this->file('themes'));
        $themes = Array();
        if ($dirs) {
            foreach ($dirs as $dir) {
                if (!$dir->isDot() && $dir->isDir()) {
                    $themes[] = $dir->getFilename();
                }
            }
        }
        $dirs = new DirectoryIterator($this->file('locale'));
        $locales = Array();
        if ($dirs) {
            foreach ($dirs as $dir) {
                if ($dir->isFile()) {
                    $locales[] = $dir->getBasename('.php');
                }
            }
        }
        $dir = new DirectoryIterator($this->file('project_images'));
        $images = Array();
        $allowed = Array('jpg','jpeg','png','gif');
        if ($dirs) {
            foreach ($dir as $image) {
                if ($image->isFile()){
                    $filename = $image->getFilename();
                    $fileinfo = pathinfo($filename);
                    if (isset($fileinfo['extension']) && in_array($fileinfo['extension'], $allowed)) {
                        $images[] = $filename;
                    }
                }
            }
        }
        
        $view->assign('themes', $themes);
        $view->assign('locales', $locales);
        $view->assign('images', $images);
        $view->assign('projects', $this->projects);
        $view->assign('folders', $this->folders);
        $view->assign('config', $this->config);
        $view->display($this->template('settings.php'));
    }
    /**
     * @brief save settings
     * @param devHelper request object
     */
    protected function save_settings(&$data) {
        switch ($data->type) {
            case 'basic': $this->save_settings_basic($data);
                break;
            case 'projects': $this->save_settings_projects($data);
                break;
            case 'folders': $this->save_settings_folders($data);
                break;
        }
    }
    /**
     * @brief saves basic settings
     * @param devHelper request object
     */
    protected function save_settings_basic(&$data) {
        if ($data->theme) {
            $this->config->theme = $data->theme;
        }
        if ($data->locale) {
            $this->config->locale = $data->locale;
        }
        if ($data->tooltips) {
            $this->config->tooltips = $data->tooltips;
        }
        $this->config->allow_ip = array_flip(array_flip(explode(',',str_replace(' ','',$data->allow_ip))));
        
        $this->save_config($this->config);
        
        $this->locale = new devLocale(@include $this->file('locale/' . $this->config->locale . ".php"));
        $this->notify($this->locale->item_updated, 1);
    }
    /**
     * @brief save folders
     * @param devHelper request object
     */
    protected function save_settings_folders(&$data) {
        switch ($data->action) {
            case 'create':
                if ($data->name && $data->path && $data->pattern) {
                    if (file_exists($data->path)) {
                        $folder = Array(
                            'name' => $data->name,
                            'path' => $data->path,
                            'pattern' => $data->pattern,
                            'exclude' => explode(',',str_replace(' ','',$data->exclude))
                        );
                        $this->config->folders[] = $folder;
                        $data->delete('name');
                        $data->delete('path');
                        $data->delete('pattern');
                        $data->delete('exclude');

                        $this->save_config($this->config);
                        $this->notify($this->locale->item_added,1);
                    } else {
                        $this->notify($this->locale->folder_does_not_exists, 3);
                    }
                }
                break;
            case "update":
                if ($data->name && $data->path && $data->pattern) {
                    if (file_exists($data->path)) {
                        $folder = Array(
                            'name' => $data->name,
                            'path' => $data->path,
                            'pattern' => $data->pattern,
                            'exclude' => explode(',',str_replace(' ','',$data->exclude))
                        );
                        $this->config->folders[$data->id] = $folder;
                        $this->save_config($this->config);
                        $this->notify($this->locale->item_updated, 1);
                    } else {
                        $this->notify($this->locale->folder_does_not_exists, 3);
                    }
                }
                break;
            case "delete":
                $this->delete_item('folders', $data);
                break;
            case 'promote':
                $this->promote_item('folders',$data);
                break;
            case 'demote':
                $this->demote_item('folders', $data);
                break;
        }
    }
    /**
     * @brief save projects
     * @param devHelper request object
     */
    protected function save_settings_projects(&$data) {
        switch ($data->action) {
            case 'create':
                if ($data->name && $data->path) {
                    $project = Array(
                        'name' => $data->name,
                        'path' => $data->path,
                        'icon' => $data->icon,
                        'active' => true
                    );
                    $this->config->projects[] = $project;
                    unset($data->name);
                    unset($data->path);
                    unset($data->icon);
                    unset($data->active);
                }
                $this->save_config($this->config);
                $this->notify($this->locale->item_added,1);
                break;
            case "update":
                if ($data->name && $data->path) {
                    $project = Array(
                        'name' => $data->name,
                        'path' => $data->path,
                        'icon' => $data->icon,
                        'active' => $data->active ? true : false
                    );
                    $this->config->projects[$data->id] = $project;
                }                
                $this->save_config($this->config);
                $this->notify($this->locale->item_updated, 1);
                break;
            case "delete":
                $this->delete_item('projects', $data);
                break;
            case 'promote':
                $this->promote_item('projects',$data);
                break;
            case 'demote':
                $this->demote_item('projects', $data);
                break;
        }
    }
    protected function delete_item($type, $data){
        if ( $data->confirm ){
                unset($this->config->{$type}[$data->id]);
                $this->save_config($this->config);
                $this->notify($this->locale->item_deleted, 2);
            }else{
                $confirm = '<form action="?page=settings" method="post">
                    <p>'.$this->locale->confirm_delete.'
                        <input type="submit" value="'.$this->locale->delete.'" />
                        <a href="?page=settings">'.$this->locale->cancel.'</a>
                        <input type="hidden" name="type" value="'.$type.'" />
                        <input type="hidden" name="action" value="delete" />
                        <input type="hidden" name="id" value="'.$data->id.'" />
                        <input type="hidden" name="confirm" value="1" />
                    </p>
                    </form>';
                $this->notify( $confirm ,2);
            }
    }
    protected function demote_item($type, $data){
        $count = count ($this->config->{$type});
        if ($data->id < $count){
            $new_id = $data->id + 1;
            $tmp = $this->config->{$type}[$new_id];
            $this->config->{$type}[$new_id] = $this->config->{$type}[$data->id];
            $this->config->{$type}[$data->id] = $tmp;
            $this->save_config($this->config);
            $this->notify($this->locale->item_updated, 1);
        }
    }
    protected function promote_item($type, $data){
        if ($data->id > 0){
            $new_id = $data->id - 1;
            $tmp = $this->config->{$type}[$new_id];
            $this->config->{$type}[$new_id] = $this->config->{$type}[$data->id];
            $this->config->{$type}[$data->id] = $tmp;
            $this->save_config($this->config);
            $this->notify($this->locale->item_updated, 1);
        }
    }

    protected function install() {
        $this->config = new devHelper(Array('theme' => 'default', 'locale' => 'en'));        
        $this->config->folders=Array(
            Array(
                'name'=>'www',
                'path'=>$_SERVER['DOCUMENT_ROOT'],
                'pattern'=>'/%FOLDER%/'
            )
        );
        $this->save_config($this->config);       
    }
    /**
     * @brief get app file
     * @param string $file filename
     * @return string
     */
    protected function file($file=false) {
        if (!isset($this->dir)) {
            $dir = dirname(__FILE__);
            $this->dir = $dir . "/";
        }
        return $this->dir . $file;
    }
    /**
     * @brief get theme's template file uri
     *
     * if file is not found, falls back to the default theme's file
     *
     * @param string $file filename
     * @return string
     */
    protected function template($file) {
        $template = $this->file("themes/" . $this->config->theme . "/" . $file);
        if (!file_exists($template) && $this->config->theme != 'default'){
            $template = $this->file("themes/default/" . $file);
        }
        return $template;
    }
    /**
     * @brief save config to configuration file
     * @param array $config
     */
    protected function save_config($config) {
        $config->projects = $config->projects ? array_values($config->projects) : false;
        $config->folders = $config->folders ? array_values($config->folders) : false;
        if (!file_put_contents($this->configfile, serialize($config))) {
            throw new Exception('failed to save config data to' . $this->file($this->configfile));
        } else {
            $this->config = $config;
            $this->projects = $this->formatProjects($config->projects);
            $this->folders = $this->formatFolders($config->folders);
        }
    }
    /**
     * @brief get View
     * @return devView
     */
    protected function get_view() {
        $view = new devView();
        $view->assign('theme_dir', $this->url . "themes/" . $this->config->theme . "/");
        $view->assign('request', $this->request);
        $view->assignRef('locale', $this->locale);
        return $view;
    }
    /**
     * @brief send notification message
     * @param string $msg
     * @param int $level
     */
    protected function notify($msg, $level = 0) {
        $this->notices[] = Array('message' => $msg, 'level' => $level);
    }

}
