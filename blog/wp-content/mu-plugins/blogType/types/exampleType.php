<?php
/**
 * @name Plugin  exampleType
 * @abstract Plugin to implement blogType and modify wordpress for any blogType, except defined in files
 * @author Antoni Bertran (abertranb@uoc.edu)
 * @copyright 2010 Universitat Oberta de Catalunya
 * @license GPL
 * @version 1.0.0
 * Date December 2010
*/

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'blogType.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'UtilsPropertiesWP.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'defaultType.php');

class exampleType extends defaultType implements blogType {
	
	private $configuration = null;
	
	public function __construct() {
		//Loads configuration
		$this->configuration = new UtilsPropertiesWP(dirname(__FILE__).'/../configuration/'.get_class($this).'.cfg');
	
	}
	
	/* You can define as load parent function */
	public function getCourseName($blti) {
		return parent::getCourseName($blti);
	}

	/* exampleType extends from defaultType, then if it's not defined the server loads from parent (better than load as parent::getCoursePath
	public function getCoursePath($blti, $siteUrlArray, $domain) {
	
	    

	}
	
	public function setLanguage($blti){
		
	}
	*/
	/**
	 * Change the default theme to twentyeleven
	 * @see defaultType::changeTheme()
	 */
	public function changeTheme() {
		switch_theme("twentyeleven", "twentyeleven"); //change theme
	}
	
	/**
	 * Loads akismet plugin
	 * @see defaultType::loadPlugins()
	 */
	public function loadPlugins() {
		$plugins = $this->removeActivedPlugins(array("akismet/akismet.php"));
		activate_plugins($plugins,'');
	}
	
	/**
	 * Change the default role Mapping
	 * @see defaultType::roleMapping()
	 */
	public function roleMapping($role, $blti) {
	 
		if (strpos($role , 'Administrator')===TRUE) return "administrator";
	    if (strpos($role, 'Instructor')===TRUE) return "editor";
		
    	return "contributor";
	}

    /**
     * This function contains the last actions before show blog
     */
    public function postActions($obj) {
    	if (isset($obj->blog_is_new) && $obj->blog_is_new) {
    		// update general options //
    		update_option("blogname",$this->configuration->getProperty("blogname"));
    	}
    }
}