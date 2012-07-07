<?php
  
class PTX_Weather_Location {

    /*
    * Location name (Private).
    * 
    * name of the location we are trying to find forecast for
    */    
    private $_location = null;

    /*
    * Location hash (Private).
    *   
    * holds hash value of the location
    */
    private $_location_hash = null;

    /*
    * Tmp Folder (Private).
    * 
    * path to folder where our tmp files are stored
    */
    private $_tmp_folder = '/tmp/locations';

    /**
    * Wunderground Settings.
    * 
    * settings for wunderground.    
    * @see http://www.wunderground.com/weather/api/d/documentation.html
    */
    private $_wunderground_settings = array(
        'lang' => 'EN',
        'refresh' => 1800, // value is seconds.
        'api_key' => '',
        'url' => 'http://api.wunderground.com',
        'geolookup' => false,
        'conditions' => false,
        'astronomy' => false,
        'forecast' => false,
        'astronomy' => false,
        'radar' => false,
        'animatedradar' => false,
        'satellite' => false,
        'animatedsatellite' => false,
        'webcams' => false,
        'history' => false,
        'alerts' => false,
        'hourly' => false,
        'hourly10day' => false,
        'forecast10day' => false,
        'yesterday' => false,
        'planner' => false,
        'autocomplete' => false,
        'almanac' => false,
        'tide' => false,
        'rawtide' => false,
        'currenthurricane' => false);

    /*
    * XML (Private).
    * holds information about location.
    */
    private $_xml = array();

    /*
    * XML Path (Private).
    * 
    * path for file where our xml is stored.
    */
    private $_xml_path = '';

    /**
    * Constructor.
    * 
    * constructor of the class
    * @param string $location - name of location
    * @param array $options - additional options for class
    */
    public function __construct($location, array $options = array()) {
        // Set variables.
        $this->_location = $location;
        $this->_location_hash = md5($location);
        $this->_xml_path = APPLICATION_PATH.$this->_tmp_folder.'/'.$this->_location_hash.'.xml';
        
        // Update url.
        if(isset($options['wunderground'])) {
            $this->_wunderground_settings = array_merge($this->_wunderground_settings,$options['wunderground']);
        }                   

        // Check for json file.
        $this->_get_xml();
    }

    /**
    * Get Xml.
    * 
    * returns xml.
    * 
    */
    private function _get_xml() {
        $this->_refresh_xml();
        
        $content = file_get_contents($this->_xml_path);
        $this->_xml = unserialize($content);
        debug($this->_xml);        
    }

    /**
    * Get Wunderground Url (Private).
    * 
    * returns url
    * @return string url to be called.
    */
    private function _get_wunderground_url() {
        // API key.
        $url = $this->_wunderground_settings['url'].'/api/'.$this->_wunderground_settings['api_key'];
        
        // Features.
        $features = array('geolookup','conditions','astronomy','forecast','astronomy','radar','animatedradar',
            'satellite','animatedsatellite','webcams','history','alerts','hourly','hourly10day',
            'forecast10day','yesterday','planner','autocomplete','almanac','tide','rawtide','currenthurricane');

        foreach($features as $key => $value) {
            if($this->_wunderground_settings[$value] == true) {
                $url .= '/'.$value;        
            }
        }
        
        // Lang.
        if(!empty($this->_wunderground_settings['lang'])) {
            $url .= '/lang:'.$this->_wunderground_settings['lang'];
        }
        
        // Location.
        $url .= '/q/'.$this->_location.'.xml';
        
        // Return.
        return (string)$url;
    }

    /**
    * Refresh Xml (Private).
    * 
    * retrieve data from main website and saves them into the file
    */
    private function _refresh_xml() {
        // Variables.
        $reload = true;
        
        // Do we need to get newer info ?
        if(!file_exists($this->_xml_path)) {
            $reload = true;
        } else {
            $limit = (time()-$this->_wunderground_settings['refresh']);
            $file_created = filemtime($this->_xml_path);
            
            if($file_created < $limit) {
                $reload = true;
            }
        }

        // We must get newer info
        if($reload) {                       
            $url = $this->_get_wunderground_url();
            if(!($content = file_get_contents($url))) {
                throw new PTX_Exception(sprintf('URL - %s - could not be retrieved.', $url));
            } else {
                // Save it.
                $xml_2_array = PTX_Xml_Parser::xml2array($content);

                if(!isset($xml_2_array['response']) || empty($xml_2_array['response'])) {
                    throw new PTX_Exception(sprintf('Nothing has been found for location: %s', $this->_location));
                } else if(isset($xml_2_array['response']['results'])) {
                    $locations = null; 
                    foreach($xml_2_array['response']['results']['result'] as $key => $values) {
                        $locations .= $values['country'].'/'.$values['city'].', ';
                    }
                    throw new PTX_Exception(sprintf('You must be more precise with location. Multiple locations have been found: %s', substr(trim($locations),0,-1)));
                } 
                
                if(!($fopen = fopen($this->_xml_path,'w+'))) {
                    throw new PTX_Exception(sprintf('I was not able to open file: %s', $this->_xml_path));
                } else if(!(fwrite($fopen,serialize($xml_2_array)))) {
                    throw new PTX_Exception(sprintf('I was not able to write into file: %s', $this->_xml_path));
                } else if (!(fclose($fopen))) {
                    throw new PTX_Exception(sprintf('I was not able to close file: %s', $this->_xml_path));
                }
            }
        }
    }
}
