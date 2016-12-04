<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Nbdesigner_DebugTool {
    /**
     * Before use log() enable config log in wp-config.php in root folder
     * If can't modified wp-config.php use function wirite_log() or manual_write_debug()
     * @param type $data
     */
    static private $_path = NBDESIGNER_PLUGIN_DIR;
    public function __construct($path = ''){
        if($path != ''){
            self::$_path = $path;
        }else{
            self::$_path = NBDESIGNER_PLUGIN_DIR;
        }       
    }
    public static function log($data){
        if(NBDESIGNER_MODE_DEBUG === 'dev'){
            ob_start();
            var_dump($data);
            error_log(ob_get_clean());
        }else{
            return FALSE;
        }
    }
    public static function wirite_log($data){
        if(NBDESIGNER_MODE_DEBUG === 'dev'){
            error_reporting( E_ALL );
            ini_set('log_errors', 1);
            ini_set('error_log', self::$_path . 'debug.log');           
            error_log(basename(__FILE__) . ': Start debug.');
            ob_start();
            var_dump($data);
            error_log(ob_get_clean());            
            error_log(basename(__FILE__) . ': End debug.');
        }else{
            return FALSE;
        }        
    }
    public static function manual_write_debug($data){
        $path = self::$_path . 'debug.txt';
        $data = print_r($data, true);
        if (NBDESIGNER_MODE_DEBUG === 'dev') {
            if (!$fp = fopen($path, 'w')) {
                return FALSE;
            }
            flock($fp, LOCK_EX);
            fwrite($fp, $data);
            flock($fp, LOCK_UN);
            fclose($fp);
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public static function manual_write_debug2($data){
        $data = print_r($data, true);
        $path = self::$_path . 'debug.txt';    
        file_put_contents($path, $data);
    }
    public static function console_log($data){
        echo '<script>';
        echo 'console.log('. json_encode( $data ) .')';
        echo '</script>';        
    }
    public static function theme_check_hook(){
        //TODO
    }
    public static function update_data_migrate_domain(){
        if (!wp_verify_nonce($_POST['_nbdesigner_migrate_nonce'], 'nbdesigner-migrate-key') || !current_user_can('administrator')) {
            die('Security error');
        } 
        $result = array();
        if(isset($_POST['old_domain']) && $_POST['old_domain'] != '' && isset($_POST['new_domain']) && $_POST['new_domain'] != ''){
            $old_domain = rtrim($_POST['old_domain'], '/');
            $new_domain = rtrim($_POST['new_domain'], '/');
            $upload_dir = wp_upload_dir();
            $path = $upload_dir['basedir'] . '/nbdesigner/';            
            $files = array("arts", "fonts");
            $path_backup_folder = $path . 'backup';
            if(!file_exists($path_backup_folder)) wp_mkdir_p ($path_backup_folder);
            $_files = glob($path_backup_folder.'/*');
            foreach($_files as $file){ 
              if(is_file($file)) unlink($file); 
            }   
            $result['flag'] = 1;
            $result['mes'] = "Success!";             
            foreach ($files as $file){
                $fullname = $path . $file . '.json';    
                if (file_exists($fullname)) {
                    $backup_file = $path_backup_folder . '/' . $file . '.json';
                    if(copy($fullname,$backup_file)){
                        $list = json_decode(file_get_contents($fullname));  
                        foreach ($list as $l){
                            $name_arr = explode('/uploads/', $l->file);
                            $new_file_name = $upload_dir['basedir'] . '/' . $name_arr[1];
                            $new_url = str_replace($old_domain, $new_domain, $l->url);
                            $l->file = $new_file_name;
                            $l->url = $new_url;
                        }
                        if(!file_put_contents($fullname, json_encode($list))){
                            $result['flag'] = 0;
                            $result['mes'] = "Erorr write data!";                             
                        }
                    }else{
                        $result['flag'] = 0;
                        $result['mes'] = "Erorr backup!";                        
                    }
                }
            }           
        }else{
            $result['flag'] = 0;
            $result['mes'] = "Invalid info!";
        }
        echo json_encode($result);
        wp_die();
    }
    public static function restore_data_migrate_domain(){
        if (!wp_verify_nonce($_POST['nonce'], 'nbdesigner_add_cat') || !current_user_can('administrator')) {
            die('Security error');
        } 
        $result = array();
        $result['flag'] = 1;
        $result['mes'] = "Success!";     
        $upload_dir = wp_upload_dir();
        $path = $upload_dir['basedir'] . '/nbdesigner/';          
        $files = array("arts", "fonts");
        foreach ($files as $file){
            $fullname = $path . $file . '.json';    
            $backup = $path .'backup/'. $file . '.json';    
            if (file_exists($fullname) && file_exists($backup)) {
                if(unlink($fullname)){
                    copy($backup,$fullname);
                }
            }else{
                $result['flag'] = 0;
                $result['mes'] = "Files not exist!";                 
            }
        }
        echo json_encode($result);
        wp_die();        
    }
}