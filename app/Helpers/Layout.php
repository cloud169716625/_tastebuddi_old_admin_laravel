<?php

namespace Helpers;

class Layout
{
	
	protected $scripts  = array(); 
	protected $styles   = array();
	protected static $instances = array();

	/**
	 * @param string $key
	 * @return static
	 */
	public static function instance( $key = 'default' ){		

		if( isset( static::$instances[ $key ] ) ){
			return static::$instances[ $key ];
		}
		
		return static::$instances[ $key ] = new static;
	}

	public static function absPath( $path ){
		$subdir = env( 'SUBDIR' ) ? '/'.env( 'SUBDIR' ): '';
		$path = substr( $path , 0 ,1 ) != '/' ? '/'.$path : $path;
		return $subdir.$path;
	}

	/**
	 * add single page scripts
	 * @param string $script_path
	 */
	public function addScript( $script_path , $key = null ){
		if( $key ){
			$this->scripts[ $key ] = 	$script_path;
		}else{
			$this->scripts[] = 	$script_path;
		}
	}
	
	/**
	 * add style 
	 * @param string $script_path
	 */
	public function addStyle( $style_path ){
		$this->styles[] = 	$style_path;
	}

	public function addComponent( $component )
	{

		$component_file  = $component.'.js';
		$path = str_replace( '_', '/', $component );
		/**
		 * @TODO create a components path here
		 */

	}
	
	public function getScripts(){
		return $this->scripts;
	}
	
	public function getStyles(){
		return $this->styles;
	}

	/**
	 * Render a single script
	 *
	 * @param $path
	 * @return string
	 */
	public function renderScript( $path ){

		if( substr( $path , 0 , 4 ) == 'http' ){
			return '<script src="'.$path.'"></script>'."\r";
		}

		$subdir = env('SUBDIR') ? '/'.env('SUBDIR'): '';
		$path = substr( $path , 0 , 1 ) == '/' ? $path : '/'.$path;

		return '<script type="text/javascript" src="'.$subdir.$path.'"></script>'."\r";
	}

	/**
	 * Render a single style
	 * @param $path
	 * @return string
	 */
	public function renderStyle( $path ){

		if( substr( $path , 0 , 4 ) == 'http' ){
			return '<link rel="stylesheet" href="'.$path.'">'."\r";
		}

		$subdir = env('SUBDIR') ? '/'.env('SUBDIR'): '';
		$path 	= substr( $path , 0 , 1 ) == '/' ? $path : '/'.$path;

		return '<link rel="stylesheet" href="'.$subdir.$path.'">'."\r";
	}

	/**
	 *
	 * @return string
	 */
	public function renderPageScripts(){
		$html = array();
		$s_array = [];

		foreach( $this->getScripts() as $script ){
			if( in_array( $script , $s_array) ){
				continue;
			}
			$s_array[] = $script;
			$html[] = $this->renderScript( $script );
		}

		return implode( "" , $html );
	}

	/**
	 * @return string
	 */
	public function renderPageStyles(){
		$html = array();
		$s_array = [];

		foreach( $this->getStyles() as $style ){
			if( in_array( $style , $s_array) ){
				continue;
			}
			$s_array[] = $style;
			$html[] = $this->renderStyle( $style );
		}

		return implode( "\r\n" , $html );
	}

	public static function placeholderImage(){
		return '/images/placeholder.jpg';
	}

	public static function loadGMap( )
	{
		static::instance()->addScript( 'https://maps.googleapis.com/maps/api/js?key='.env( 'GMAP_API_KEY' ) );
	}

	public static function loadDropzone()
	{
        static::instance()->addStyle( 'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/dropzone.css' );
        static::instance()->addScript( 'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/dropzone.js' );
	}

	public static function loadTrumbow()
	{
		static::instance()->addStyle( 'https://cdn.jsdelivr.net/npm/trumbowyg@2/dist/ui/trumbowyg.min.css' );
		static::instance()->addScript( 'https://cdn.jsdelivr.net/npm/trumbowyg@2' );
	}

	public static function loadTouch()
	{
		static::instance()->addScript( 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js' );
	}

	public static function loadHammer()
	{
		static::instance()->addScript( '/js/plugins/hammerjs/hammerjs.js' );
	}

	public static function loadToastr()
	{
		static::instance()->addStyle( '/js/plugins/toastr/toastr.min.css' );
		static::instance()->addScript( '/js/plugins/toastr/toastr.min.js' );
	}

	public static function fileuploadloadFileupload(){
		static::instance()->addScript( '/js/plugins/fileupload/js/vendor/jquery.ui.widget.js' );
		static::instance()->addScript( '/js/plugins/fileupload/js/jquery.iframe-transport.js' );
		static::instance()->addScript( '/js/plugins/fileupload/js/jquery.fileupload.js' );
	}

    public static function loadVue()
    {
        if(  in_array( strtolower( env( 'APP_ENV' ) ), ['prod', 'production'] )){
            static::instance()->addScript( '/js/plugins/vue/vue-prod.2.6.10.js' );
        }else{
            static::instance()->addScript( '/js/plugins/vue/vue-dev.2.6.10.js' );
        }
    }

	public static function loadBlockUI()
	{
		static::instance()->addScript( '/js/plugins/blockui/blockui.js' );
	}

	public static function loadTablr()
	{
		static::instance()->addStyle( '/plugins/tablr/tablr.css' );
		static::instance()->addScript( '/plugins/tablr/tablrn.js' );
	}

	public static function loadSlider()
	{
		static::instance()->addStyle( '/js/plugins/sliders/ion.rangeSlider.min.css' );
		static::instance()->addScript( '/js/plugins/sliders/ion.rangeSlider_2019.min.js' );
	}

	public static function loadSwitchery()
	{
		static::instance()->addStyle( 'https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.css' );
		static::instance()->addScript( 'https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.js' );
	}

	public static function loadAutoComplete()
	{
		static::instance()->addScript( '/js/plugins/autocomplete/auto-complete.min.js' );
	}

	public static function loadJqueryUI()
	{
		static::instance()->addStyle( '/js/plugins/jquery_ui/jquery-ui.min.css' );
		static::instance()->addScript( '/js/plugins/jquery_ui/jquery-ui.min.js' );
	}

    public static function loadFileupload( $file_type = null ){
        static::instance()->addScript( '/js/plugins/fileupload/js/vendor/jquery.ui.widget.js' );
        static::instance()->addScript( '/js/plugins/fileupload/js/jquery.iframe-transport.js' );
        static::instance()->addScript( '/js/plugins/fileupload/js/jquery.fileupload.js' );
    }

	public static function loadJqueryMobile()
	{
		static::instance()->addScript( '/js/plugins/jquery/jquery-mobile.min.js' );
	}

    /**
     * Load the component from the Controller
     *
     * @param $path
     */

	public static function loadComponent( $path )
	{
	    $theme = env( 'APP_THEME' , 'appetiser' );
	    $path = str_replace( '.', '/' , $path );

	    $component_path     = base_path( 'resources/views/themes/'.$theme.'/components/'.$path );
	    $component_path     = substr( $component_path, -3 ) != '.js' ? $component_path.'.js' : $component_path;

	    if(  is_file( $component_path ) ){
            static::instance()->addScript( $component_path );
        }

	}

	public static function loadPagination( $design = 1 )
	{
		/**
		view()->addLocation( __DIR__.'/Views/' );
		$design = 'pagination'.$design;
		return view( 'Pagination.'.$design );
		**/
	}

    /**
     * Set the sidemenu of a layout
     * @param string $user_type
     * @param array $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public static function sideMenu( $user_type = 'admin' , $data = [] )
	{

	    $view  = 'sidemenu.sidemenu_'.$user_type;
        return  view( $view )->with( $data );
	}

	public static function breadcrumb( $crumbs = [] )
	{
        return  view( 'breadcrumb.breadcrumb' )->with( 'crumbs', $crumbs );
	}

	/**
	 * @param $text
	 * @param bool $strict
	 * @return mixed|string
	 */

	public static function slugify($text,$strict = false) {
		$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
		// replace non letter or digits by -
		$text = preg_replace('~[^\\pL\d.]+~u', '-', $text);

		// trim
		$text = trim($text, '-');
		setlocale(LC_CTYPE, 'en_GB.utf8');
		// transliterate
		if (function_exists('iconv')) {
			$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
		}

		// lowercase
		$text = strtolower($text);
		// remove unwanted characters
		$text = preg_replace('~[^-\w.]+~', '', $text);
		if (empty($text)) {
			return 'empty_$';
		}
		if ($strict) {
			$text = str_replace(".", "_", $text);
		}
		return $text;
	}

}