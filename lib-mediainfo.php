<?php 
/* 

Source:
http://www.phpclasses.org/package/7788-PHP-Retrieve-the-details-of-video-files-with-mediainfo.html

2016-02 - Modified by David Swinton to hadnle multiple audio tracks and subtitles

 * purpose of this class is to retrieve media info from movie/video files 
 * NO media file is provided; so you have to use of your own. 
 * "mediainfo" dependancy (MEDIAINFO must be installed ...http://mediainfo.sourceforge.net - for ubuntu: sudo apt-get install mediainfo) 
 * Tested on debian/ubuntu 
 * 
*/ 
class mediaInfo{ 
    var $filename; 
    var $media_data; 
    var $arrGeneral; 
    var $arrVideo; 
    var $arrAudio; 
    var $arrText; 
    
    /* 
     * Initialize the class 
     * Get the media info of the passed file 
     */ 
    function __construct($filename = ''){ 
        
        $mediainfo = trim(shell_exec('type -P mediainfo')); 
        if (empty($mediainfo)){ 
            die('<h1>Mediainfo is not available</h1>'); 
        } 
        
        if($filename != ''){ 
            $this->filename = trim($filename); 
            if(!file_exists($this->filename)) die('File does not exists.'); 
            $this->filename = escapeshellarg($this->filename); 
            $this->media_data = shell_exec("mediainfo $this->filename"); 
        } 
        
        $this->make_info_array(); 
        
    } 

    /* 
     * Print a PREformatted info of the media file 
     */ 
    function print_media_info(){ 
        
        echo('<pre>'.$this->media_data.'</pre>'); 
        
    } 

    /* 
     * Makes 3 arrays with general, video and audio info 
     */ 
    function make_info_array(){ 
            
        $arrData = explode(chr(10),$this->media_data); 
        $general = TRUE; 
        $audio = FALSE; 
        $video = FALSE; 
        $text = FALSE; 
        $count = 0; 
		$groupKey = 0;
            
        if(is_array($arrData)){  
            foreach($arrData as $key=>$val){ 
                $arrProperty = explode(': ',$val); 
				if(trim($arrProperty[0]) == "ID"){$groupKey = $arrProperty[1];};
                if(array_key_exists(1, $arrProperty) && TRUE === $general && $count == 0) $this->arrGeneral[trim($arrProperty[0])] = trim($arrProperty[1]); 
                if(array_key_exists(1, $arrProperty) && TRUE === $video && $count == 1) $this->arrVideo[trim($arrProperty[0])] = trim($arrProperty[1]); 
                if(array_key_exists(1, $arrProperty) && TRUE === $audio && $count == 2) $this->arrAudio[$groupKey][trim($arrProperty[0])] = trim($arrProperty[1]); 
                if(array_key_exists(1, $arrProperty) && TRUE === $text && $count == 3) $this->arrText[$groupKey][trim($arrProperty[0])] = trim($arrProperty[1]); 
                if(trim($arrProperty[0]) == 'Video' && !array_key_exists(1, $arrProperty)){$general = FALSE; $video = TRUE; $text = FALSE; $count++;} 
                if(trim($arrProperty[0]) == 'Audio' && !array_key_exists(1, $arrProperty)){$video = FALSE; $audio = TRUE; $text = FALSE; $count++;} 
                if(trim(substr($arrProperty[0],0,4)) == 'Text' && !array_key_exists(1, $arrProperty)){$video = FALSE; $audio = FALSE; $text = TRUE; $count++;} 
            } 
        
        } 
        
    } 

    /* 
     * Returns an array with the general info 
     */ 
    function get_general_info(){ 
        return($this->arrGeneral); 
    } 
    
    /* 
     * Returns an array with the video info 
     */ 
    function get_video_info(){ 
        return($this->arrVideo); 
    } 

    /* 
     * Returns an array with the audio info 
     */ 
    function get_audio_info(){ 
        return($this->arrAudio); 
    } 

    /* 
     * Returns an item from the array with the general info 
     */ 
    function get_general_property($property){ 
        if(array_key_exists($property, $this->arrGeneral)){ 
            return($this->arrGeneral[$property]); 
        }else{ 
            return('Property does not exists'); 
        } 
    } 

    /* 
     * Returns an item from the array with the video info 
     */ 
    function get_video_property($property){ 
        if(array_key_exists($property, $this->arrVideo)){ 
            return($this->arrVideo[$property]); 
        }else{ 
            return('Property does not exists'); 
        } 
    } 

    /* 
     * Returns an item from the array with the audio info 
     */ 
    function get_audio_property($property){ 
        if(array_key_exists($property, $this->arrAudio)){ 
            return($this->arrAudio[$property]); 
        }else{ 
            return('Property does not exists'); 
        } 
    } 

    /* 
     * Returns the aspect ratio of a media file 
     */ 
    function get_aspect_ratio(){ 
        return($this->arrVideo['Display aspect ratio']); 
    } 

    /* 
     * Returns the width of a media file 
     */ 
    function get_width(){ 
        return((int)str_replace(' ', '', $this->arrVideo['Width'])); 
    } 

    /* 
     * Returns the height of a media file 
     */ 
    function get_height(){ 
        return((int)str_replace(' ', '', $this->arrVideo['Height'])); 
    } 

    /* 
     * Returns the filesize of a media file 
     */ 
    function get_file_size(){ 
        return($this->arrGeneral['File size']); 
    } 

} 
?> 