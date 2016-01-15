<?php

namespace extensions\files{
    
    /* Prevent direct access */
    defined('ADAPT_STARTED') or die;
    
    class view_field_file_uploader extends \extensions\forms\view_field{
        
        public function __construct($form_data, $user_data){
            parent::__construct($form_data, $user_data);
            
            /* We need to generate an upload key which we will use to authorised the upload */
            $keys = $this->session->data('files.upload_keys');
            if (!is_array($keys)){
                $keys = array();
            }
            
            $key = md5(date('ymdhis') . rand(0, 999999));
            $keys[] = $key;
            
            /* Save the upload keys */
            $this->session->data('field.upload_keys', $keys);
            
            /* Bulid the interface */
            $this->add(new html_input(array('type' => 'hidden', 'name' => 'upload-key', 'value' => $key)));
            
        }
        
    }
    
}

?>