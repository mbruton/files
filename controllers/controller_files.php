<?php

namespace extensions\files{
    
    /* Prevent direct access */
    defined('ADAPT_STARTED') or die;
    
    class controller_files extends \frameworks\adapt\controller{
        
        public function action_upload(){
            $files = $this->files;
            $field_names = array_keys($files);
            $output = array();
            
            foreach($field_names as $field){
                $output[$field] = array();
                $keys = array_keys($files[$field]);
                foreach($keys as $key){
                    if (!is_array($files[$field][$key])){
                        $files[$field][$key] = array($files[$field][$key]);
                    }
                }
                
                for($i = 0; $i < count($files[$field]['error']); $i++){
                    switch($files[$field]['error'][$i]){
                    case UPLOAD_ERR_OK:
                        $key = md5_file($files[$field]['tmp_name'][$i]);
                        $this->file_store->set_by_file($key, $files[$field]['tmp_name'][$i], $files[$field]['type'][$i]);
                        $output[$field][$i] = array('key' => $key);
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $output[$field][$i] = array('error' => "File too big.");
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $output[$field][$i] = array('error' => "The upload didn't complete in full.");
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $output[$field][$i] = array('error' => "No file provided");
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $output[$field][$i] = array('error' => "Upload failed, was unable to find the tmp directory.");
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $output[$field][$i] = array('error' => "Upload failed, unable to write the file to disk.");
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $output[$field][$i] = array('error' => "The upload was blocked by a PHP extension.");
                        break;
                    }
                }
                //print new html_pre(print_r($files[$field], true));
            }
            
            $this->respond('files/upload', $output);
        }
        
        public function view_default(){
            $key = $this->request['key'];
            $content_type = $this->file_store->get_content_type($key);
            
            if (!is_null($content_type) && $content_type != ''){
                $if_none_match = "";
                if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) $if_none_match = str_replace("\"", "", $_SERVER['HTTP_IF_NONE_MATCH']);
                if ($if_none_match == $key){
                    header('HTTP/1.1 304 Not Modified');
                    header("Cache-Control: public");
                    header("Expires: ");
                    header("Content-Type: ");
                    header("Etag: \"{$asset_id}\"");
                    return "";
                }else{
                    $this->content_type = $content_type;
                    
                    $date = new \frameworks\adapt\date();
                    $date->goto_years(1);
                    
                    header("Cache-Control: private");
                    header("Expires: " . $date->date("D, d M Y H:i:s") . " GMT");
                    //header("Last-Modified: " . sanitize::convert_date($image->date_modified, $this->setting('datetime_format'), 'rfc1123'));
                    header("Etag: \"{$key}\"");
                    
                    return $this->file_store->get($key);
                }
            }
            
            return $this->error(404);
        }
        
        public function view_ajax(){
            $this->content_type = 'application/json';
            return json_encode($this->response);
        }
        
        public function view_test(){
            $view = new html_div(array('class' => 'col-xs-12'));
            $this->add_view($view);
            
            $view->add(new html_h1('Upload test'));
            $form = new html_form(array('action' => '/files/test', 'method' => 'post', 'enctype' => 'multipart/form-data'));
            $view->add($form);
            
            $form->add(new html_input(array('name' => 'actions', 'type' => 'hidden', 'value' => 'files/upload')));
            $form->add(new html_input(array('name' => 'field_name', 'type' => 'file')));
            $form->add(new html_input(array('name' => 'other', 'type' => 'file')));
            $form->add(new html_input(array('name' => 'multi[]', 'type' => 'file')));
            $form->add(new html_input(array('name' => 'multi[]', 'type' => 'file')));
            
            $form->add(new html_input(array('type' => 'submit')));
        }
        
    }
    
}

?>