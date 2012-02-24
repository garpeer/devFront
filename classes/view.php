<?php
/**
 * @brief View class
 *
 */
class devView{
    /**
     * @brief constructor
     */
    public function __construct(){
    }
    /**
     * @brief assign value to view
     * @param string $key
     * @param string $val
     * 
     * Values can be reached by $this->key in templates
     */
    public function assign($key, $val){
        $this->$key = $val;
    }
     /**
     * @brief assign value to view by reference
     * @param string $key
     * @param string $val
     * 
     * Values can be reached by $this->key in templates
     */
    public function assignRef($key, &$val){
        $this->$key =& $val;
    }
    /**
     * @brief fetch view
     * @param string $template template file with extension
     * @return string
     */
    public function fetch($template){
        $__simpleview_template = $template ;
        unset($template);
        unset($dir);
        ob_start();
        require($__simpleview_template);
        return (ob_get_clean());
        
    }
    /**
     * @brief display view
     * @param string $template template file with extension
     */
    public function display($template){
        echo $this->fetch($template); 
        
    }
}