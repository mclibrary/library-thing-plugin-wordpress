<?php

class LibraryThingCache {
    
    private $time;
    private $workingDir;
    private $cacheDir;

    public function __construct(LibraryThingSettings $settingsObject){
        
        /* The settings object so we can call its functions */
        $this->sObj = $settingsObject;
        
        /* Reassigning to a scoped variable for access to privates */
        $this->settings = $this->sObj->getSettings();

        /* Assining cache diectiory variables */
        $this->workingDir   = dirname(__FILE__) . '/../lt-assets/' . date('Ymd') .'/';
        $this->cacheDir     = dirname(__FILE__) . '/../lt-assets/';

        $this->doCache();
	
    }

    public function doCache(){
        if($this->isCacheExpired()){
            $this->saveJSON();
            $this->cacheAssets();
        }
    }

    /* Check if cache has expired */
	public function isCacheExpired(){

        $this->time = (int) current_time('timestamp', 0);

        if ( $this->settings['timestamp'] + $this->settings['cache_length'] * 60 < $this->time ){
            return true;
        } else {
            return false;
        }

	}

    /* Returns the JSON request URL or FALSE on fail */
    public function getURL(){
        
        /* Set Array for usable settings */
        $query_string_data = array(
            'userid'        => $this->settings['user_id'],
            'key'           => $this->settings['dev_key'],
            'collection'    => $this->settings['collection'],
            'responseType'  => 'json',
            'resultsets'    => 'books',
            'max'           => $this->settings['max_rows'] * $this->settings['books_per_row'],
            'coverwidth'    => '125px',
        );

        /* Double-check for required fields */
        if(trim($query_string_data['userid']) === '' || trim($query_string_data['key']) === '' ){
            
            /* Return false if either field is blank */
            return false;
        
        } else {
            
            /* Base URL for JSON request */
            $json_url = 'http://www.librarything.com/api_getdata.php?';

            /* Add query string */
            foreach ($query_string_data as $key => $value) {
                
                /* Check for empty values */
                if(trim($value) !== ''){
                   $json_url .= $key . '=' . $value . '&';
                    }
                }
            }
            /* Return the final request URL */
            return $json_url;
        }


    public function getJSON(){
        $url = $this->getURL();

        if ($url){
            $json = file_get_contents($url,0,null,null);

            /* If JSON returns with a value */
            if ($json) {

                /*Make JSON Object to extract book data*/
                $books_obj = json_decode($json)->books;

            /*Re-encode as text for storage*/
            return json_encode($books_obj);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function saveJSON(){
        /* Retrieve JSON object */
        $json = $this->getJSON();

        
        if ( $json ){
            /* Update settings object */
            $this->sObj->updateSetting('json_object', $json);
            $this->sObj->updateSetting('timestamp', $this->time);
            $this->settings = $this->sObj->getSettings();
        }

    }

    public function cacheAssets(){

        /* Objectify stored JSON */
        $books = json_decode(urldecode($this->settings['json_object']));
        $this->saveImageFiles($books, $this->date);
        $this->cleanupOldFiles($this->date);

    }


    public function saveImageFiles($books, $date){
        if(!is_dir($this->workingDir)) {
            wp_mkdir_p($this->workingDir);
        }

        foreach ($books as $book) {
        $local_file_path = $this->workingDir . $book->book_id . '.jpg';
                if (!file_exists($local_file_path)){
                        $image_to_fetch = file_get_contents($book->cover);
                        //save it
                        $local_image_file = fopen($local_file_path, 'w+');
                        chmod($local_file_path ,0777);
                        fwrite($local_image_file, $image_to_fetch);
                        fclose($local_image_file);
                    }
                }
        }

    /* Removes Directory from Pevious Day */
    public function cleanupOldFiles($date){
        foreach (glob($this->cacheDir . "*/") as $filename) {
            /* Extract eight-digit filename */
            if($filename !== $this->workingDir){
                foreach (glob($filename . '/*') as $image) {
                   unlink($image);
                }
                rmdir($filename);
            }
        }
    }
}

?>