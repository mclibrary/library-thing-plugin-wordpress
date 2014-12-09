<?php


class LibraryThingWidget {

	private $sObj;
	private $settings;

	public function __construct(LibraryThingSettings $settingsObject){

		/* The settings object so we can call its functions */
        $this->sObj = $settingsObject;
        
        /* Reassigning to a scoped variable for access to privates */
        $this->settings = $this->sObj->getSettings();

		add_action('wp_enqueue_scripts', array($this, 'EnqueueLibthingScripts')); 
        add_shortcode( 'librarything', array($this, 'printWidget' ));

	}
	
	public function printWidget(){

        /* Objectify stored JSON */
        $books = json_decode(urldecode($this->settings['json_object']));
        $output = '<div class="library-thing-container">';

            $i = 1;

            foreach ($books as $book) {
                if ($i % $this->settings['books_per_row'] === 1){
                    $output .= '<div class="lt-row">';
                    $output .= $this->lt_make_cover($book);
                    $i++;
                }
                elseif ($i % $this->settings['books_per_row'] === 0){
                    $output .= $this->lt_make_cover($book);
                    $output .= '</div>';
                    $i++;
                } else {
                    $output .= $this->lt_make_cover($book);
                    $i++;
                }
            }

        $output .= '</div>';

        return $output;
        }

    public function lt_make_cover($obj){

    	$baseURL = plugins_url( null , dirname(__FILE__)) . '/lt-assets/';

        /* dbaker 12/9/14 - Styled from ext CSS using display: table */
        /*$books = $this->settings['books_per_row'];
        $books = 100 / $books;
        $books = $books - 2;
        $style = 'style="width:' . $books . '%"';*/

        /* dbaker 09-26-14 OCLC number lookup */
        $isbn = $obj->ISBN_cleaned;
//      $oclcNum = $this->getOclcNumber($isbn);
        
         
        $output = '<a class="lt-cover" ' . /*$style .*/ ' href="http://milligan.worldcat.org/search?q=' . $obj->ISBN_cleaned . '&amp;scope=1" target="_blank">'; 
// Use this when ready
//      $output = '<a class="lt-cover" ' . $style . ' href="http://milligan.worldcat.org/oclc/' . $oclcNum . '" target="_blank">';       
 
        $output .= '<img src="' . $baseURL . date('Ymd') . '/' . $obj->book_id . '.jpg" alt="' . htmlspecialchars($obj->title) . '" />';
        $output .= '</a>';

        return $output;
    }

    public function EnqueueLibthingScripts(){
        wp_enqueue_style('libthing', plugins_url( '/public/libthing.css' , dirname(__FILE__) ), false, '20121227');
        wp_enqueue_script( 'libthing-js', plugins_url( '/public/libthing.js' , dirname(__FILE__) ), array( 'jquery' ), 20130104, true );

    }
    
    /* dbaker 09-26-14 -- use WorldCat Search API to get JSON object and return OCLC number of resource */
    /* This function needs to be executed when cache is refreshed. It takes too long to request the JSON info for each cover on the page load */
    public function getOclcNumber($isbn) {
      $url = "http://www.worldcat.org/webservices/catalog/content/libraries/isbn/" . $isbn . "?oclcsymbol=TMJ&wskey=wlqnFvygpkIsvc6mwFKBA1MoFzLZDl9ziyAQ0IXxTdXopLJOTIQW2lQClwMYmc4tQ92HZqtX3hhQpLI0&format=json";
      $jsonStr = file_get_contents($url);
      
      if ($jsonStr) {
         $oclcNum = json_decode($jsonStr)->OCLCnumber;
         return $oclcNum;
      }
      else {
         return false;
      } 
    }
}



?>