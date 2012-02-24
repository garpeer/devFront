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
     * @var array
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
            $_REQUEST['is_local'] = $_SERVER['REMOTE_ADDR'] == '127.0.0.1' ? true : false;
            $this->request = new devHelper($_REQUEST);
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
            }
            if (!$config) {
                throw new Exception('config file could not be read');
            } else {
                $this->config = $config;
            }

            $this->locale = new devLocale(@include $this->file('locale/' . $this->config['locale'] . ".php"));
            $this->projects = isset($this->config['projects']) ? $this->config['projects'] : null;
            if ($this->projects) {
                foreach ($this->projects as &$project) {
                    if (!isset($project['path'])) {
                        $project['path'] = "http://" . $this->servername . "/" . $id . "/";
                    }
                    if (!isset($project['icon'])) {
                        $project['icon'] = false;
                    }
                }
            }
            $this->folders = isset($this->config['folders']) ? $this->config['folders'] : null;
            $page = $this->request->page ? $this->request->page . '_page' : 'index_page';
            if (method_exists($this, $page)) {
                $this->$page();
            } else {
                header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
                echo '<h2>404 - Page not Found</h2>';
            }
            $content = ob_get_clean();
            $view = $this->get_view();
            $view->assign('title', $this->servername);
            $view->assign('content', $content);
            $view->assign('notices', $this->notices);
            $view->display($this->template('default.php'));
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
    /**
     * @brief index page
     */
    protected function index_page() {
        if ($this->projects) {
            $view = $this->get_view();
            $projects = $this->projects;
            foreach ($projects as &$project){
                $project['icon'] = $project['icon'] ? $this->url . "project_images/".$project['icon']: false;
            }
            $view->assign('projects', $projects);
            $view->display($this->template('projects.php'));
        }
        if ($this->folders) {

            foreach ($this->folders as &$folder) {
                $exclude = isset($folder['exclude']) ? $folder['exclude'] : null;
                $exclude_projects = isset($folder['exclude_projects']) ? $folder['exclude_projects'] : false;
                if (file_exists($folder['path'])){
                    $dirs = new DirectoryIterator($folder['path']);
                    foreach ($dirs as $dir){
                        $dirname = $dir->getBasename();
                        if ( (!$exclude || !in_array($dirname, $exclude)) && $dir->isDir() && !$dir->isDot() && (!$exclude_projects || !isset($projects[$dirname]))){
                            $folder['dirs'][] = $dirname;
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
        if (!$this->request->is_local){
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
        $view->assign('themes', $themes);
        $view->assign('locales', $locales);
        $view->assign('projects', $this->projects);
        $view->assign('folders', $this->folders);
        $view->assign('c_theme', $this->config['theme']);
        $view->assign('c_locale', $this->config['locale']);
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
        $theme = $data->theme;
        $locale = $data->locale;
        if ($theme) {
            $this->config['theme'] = $data->theme;
        }
        if ($locale) {
            $this->config['locale'] = $data->locale;
        }
        $this->save_config($this->config);

        $this->notify($this->locale->item_updated, 1);
        $this->locale = new devLocale(@include $this->file('locale/' . $this->config['locale'] . ".php"));
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
                            'pattern' => $data->pattern
                        );
                        $this->config['folders'][] = $folder;
                        $data->delete('name');
                        $data->delete('path');
                        $data->delete('pattern');

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
                            'pattern' => $data->pattern
                        );
                        $this->config['folders'][$data->id] = $folder;
                        $this->save_config($this->config);
                        $this->notify($this->locale->item_updated, 1);
                    } else {
                        $this->notify($this->locale->folder_does_not_exists, 3);
                    }
                }
                break;
            case "delete":
                unset($this->config['folders'][$data->id]);
                $this->save_config($this->config);
                $this->notify($this->locale->item_deleted, 2);
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
                        'icon' => $data->icon
                    );
                    $this->config['projects'][] = $project;
                    $data->delete('name');
                    $data->delete('path');
                    $data->delete('icon');
                }
                $this->save_config($this->config);
                $this->notify($this->locale->item_added,1);
                break;
            case "update":
                if ($data->name && $data->path) {
                    $project = Array(
                        'name' => $data->name,
                        'path' => $data->path,
                        'icon' => $data->icon
                    );
                    $this->config['projects'][$data->id] = $project;
                }
                $this->save_config($this->config);
                $this->notify($this->locale->item_updated, 1);
                break;
            case "delete":
                unset($this->config['projects'][$data->id]);
                $this->save_config($this->config);
                $this->notify($this->locale->item_deleted, 2);
                break;
        }
    }

    protected function install() {
        $this->config = Array('theme' => 'default', 'locale' => 'en');        
        $this->config['folders']=Array(
            Array(
                'name'=>'www',
                'path'=>$_SERVER['DOCUMENT_ROOT'],
                'pattern'=>'/%s/'
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
        $template = $this->file("themes/" . $this->config['theme'] . "/" . $file);
        if (!file_exists($template) && $this->config['theme'] != 'default'){
            $template = $this->file("themes/default/" . $file);
        }
        return $template;
    }
    /**
     * @brief save config to configuration file
     * @param array $config
     */
    protected function save_config($config) {
        if (!file_put_contents($this->configfile, serialize($config))) {
            throw new Exception('failed to save config data to' . $this->file($this->configfile));
        } else {
            $this->projects = isset ($config['projects']) ? $config['projects'] : false;
            $this->folders = isset ($config['folders']) ? $config['folders'] : false;
        }
    }
    /**
     * @brief get View
     * @return devView
     */
    protected function get_view() {
        $view = new devView();
        $view->assign('theme_dir', $this->url . "themes/" . $this->config['theme'] . "/");
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
