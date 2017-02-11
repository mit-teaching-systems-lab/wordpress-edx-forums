<?php
/**
 * @name Plugin  defaultType
 * @abstract Plugin to implement blogType and modify wordpress for any blogType, except defined in files
 * @author Antoni Bertran (abertranb@uoc.edu)
 * @copyright 2010 Universitat Oberta de Catalunya
 * @license GPL
 * @version 1.0.0
 * Date December 2010
*/

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'blogType.php');

class defaultType implements blogType {
	
	private $configuration = null;
	private $language_wp = null;
	
	/**
	 * Gets the course name
	 * @see blogType::getCourseName()
	 */
	public function getCourseName($blti) {
		return $blti->title;
	}
	
	/**
	 * get the course path
	 * @see blogType::getCoursePath()
	 */
	public function getCoursePath($blti, $siteUrlArray, $domain) {
	
	    $course = str_replace(':','-', $_POST[ 'oauth_consumer_key' ].$blti->resource_link->lti_context_id);  // TO make it past sanitize_user
	    
		return $course;
	    

	}
	
	/**
	 * 
	 * @see blogType::getMetaBlog()
	 */
	public function getMetaBlog($blti){
		$langid = '';
		        
		$langid =$blti->resource_link->getSetting('launch_presentation_locale', false);
		switch ($langid)
		{
			case "ca-ES":
			case "ca_utf8":
				$lang="ca_ES";
				$this->language_wp="ca_ES";
				break;
			case "es-ES":
			case "es_utf8":
				$lang="es_ES";
				$this->language_wp="es_ES";
				break;
			case "fr-FR":
			case "fr_utf8":
				$lang="fr_FR";
				$this->language_wp="fr_FR";
				break;
			case "ir-IR":
			case "ir_utf8":
				$lang="ir_IR";
				$this->language_wp="ir_IR";
				break;
			case "nl-FR":
			case "nl_utf8":
				$lang="nl_NL";
				$this->language_wp="nl_NL";
				break;
			case "pl-PL":
			case "pl_utf8":
				$lang="pl_PL";
				$this->language_wp="pl_PL";
				break;
			case "sv-SE":
			case "sv_utf8":
				$lang="sv_SE";
				$this->language_wp="sv_SE";
				break;
			default: 
				$lang="en_EN";
				$this->language_wp="en_EN";
			}
		        
			$meta = apply_filters('signup_create_blog_meta', array ('lang_id' => $lang, 'public' => 0)); //deprecated
		
			return $meta;
			
		}
	
		/**
		 * 
		 * @see blogType::setLanguage()
		 */
		public function setLanguage($blti){
			return update_site_option( 'WPLANG', $this->language_wp );
		}
		
		
	/**
	 * 
	 * @see blogType::changeTheme()
	 */
	public function changeTheme() {
            
	}
	
	/**
	 * 
	 * @see blogType::loadPlugins()
	 */
	public function loadPlugins() {
		
	}
	
	/**
	 * 
	 * @see blogType::roleMapping()
	 */
	public function roleMapping($role, $blti) {
	 
		$roles =$blti->user->roles;
		//Moodle indicates the admin role as 'Instructor,Administrator' 
		if ($blti->user->isAdmin()) return "administrator";
	    elseif ($blti->user->isStaff()) return "editor";
	    elseif ($blti->user->isLearner()) return "author";
	    else return "subscriber";
	    
	}

	/**
	 * 
	 * @see blogType::postActions()
	 */
    public function postActions($obj) {
     	
    }

    /**
     * 
     * Shows error if exists
     * @param unknown_type $blog_id
     * @param unknown_type $path
     */
    public function checkErrorCreatingBlog($blog_id, $path) {
	    if (isset($blog_id->errors) && count($blog_id->errors)>0) {
		    $strError = '';
		    foreach($blog_id->errors as $error){
		    	$strError .= (is_array($error)?implode(' ',$error):$errror).'<br>';
		    }
    		wp_die("BASIC LTI error creating blog $path $strError");
      	    return;
    	}
    }
    
    /**
    * Remove the plugins loaded as default
     * @param array $arrayPlugins
    */
    public function removeActivedPlugins($arrayPlugins) {
	    $array = array();
	    foreach ($arrayPlugins as $plugin) {
	    if (!is_plugin_active($plugin))
	    	$array[] = $plugin;
	    }
    	return $array;
    }
    	
}