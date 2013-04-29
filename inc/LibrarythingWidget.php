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

    	$baseURL = plugins_url('library-thing-widget/lt-assets/');

        $books = $this->settings['books_per_row'];
        $books = 100 / $books;
        $books = $books - 2;
        $style = 'style="width:' . $books . '%"';

        $output = '<a class="lt-cover" ' . $style . ' href="http://www.librarything.com/isbn/' . $obj->ISBN_cleaned . '">';
        $output .= '<img src="' . $baseURL . date('Ymd') . '/' . $obj->book_id . '.jpg" />';
        $output .= '</a>';

        return $output;
    }

    public function EnqueueLibthingScripts(){
        wp_enqueue_style('libthing', plugins_url( 'library-thing-widget/public/libthing.css'), false, '20121227');
        wp_enqueue_script( 'libthing-js', plugins_url( 'library-thing-widget/public/libthing.js'), array( 'jquery' ), 20130104, true );

    }
}



?>