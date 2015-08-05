<?php
class Functions {
    public static function init() {
        CommonDataContainer::$daysArray         = self::getDays();
        CommonDataContainer::$monthsArray       = self::getMonths();
        CommonDataContainer::$yearsArray        = self::getYears();
        CommonDataContainer::$hoursArray        = self::getHours();
        CommonDataContainer::$minutesArray      = self::getMinutes();
        CommonDataContainer::$timezonesArray    = self::getTimezones();
    }

    /******DATE/TIME ARRAYS*****/

    /*
     * Get minutes in array format with leading zeros
     * @return $arr [0] => 00, [59] => 59
     */
    public static function getMinutes() {
        $arr = array();

        array_push($arr, '00');

        for ( $minute = 1; $minute < 60; $minute++ ) {
            if ( strlen($minute) == 1 ) $minute = '0' . $minute;
            
            $arr[$minute] = $minute;
        }

        return $arr;
    }

    /*
     * Get hours in array format in 24-hour format with leading zeros
     * @return @arr [0] => 00 (Midnight), [23] => 23 (11pm)
     */
    public static function getHours() {
        $arr = array();

        $arr['00'] = '12am';

        for ( $hour = 0; $hour < 24; $hour++ ) {
            $hourValue = $hour;

            if ( $hour == 0 ) {
                $hour      = '0' . $hour;
                $hourValue = '12am';
            } elseif ( $hour > 0 && $hour < 10 ) {
                $hour      = '0' . $hour;
                $hourValue = $hour . 'am';
            } elseif ( $hour > 9 && $hour < 12 ) {
                $hourValue = $hour . 'am';
            } elseif ( $hour == 12 ) {
                $hourValue = '12pm';
            } elseif ( $hour > 12 ) {
                $hourValue = ($hour-12) . 'pm';
            }

            $arr[$hour] = $hourValue;
        }

        return $arr;
    }

    /*
     * Get months in array format with leading zeros
     * @return $arr [1] => 1 (January), [12] => 12 (December)
     */
    public static function getMonths() {
        $arr = array();

        for ( $month = 1; $month < 13; $month++ ) {
            $monthValue = $month;

            if ( $month < 10 ) { $month = '0' . $month; }

            $arr[$month] = date('F', mktime(0,0,0,$month, 1, date('Y'))) . '-' . $month;
        }

        return $arr;
    }

    /*
     * Get days in array format with leading zeros
     * @return $arr [1] => 1, [32] => 12
     */
    public static function getDays() {
        $arr = array();

        for ( $day = 1; $day < 33; $day++ ) {
            if ( strlen($day) == 1 ) $day = '0' . $day;
            $arr[$day] = $day;
        }

        return $arr;
    }

    /*
     * Get years in array format from release year to current year
     * @return $arr [0] => 2011, [5] => 2016
     */ 
    public static function getYears() {
        $arr          = array();
        $current_year = date("Y");

        for ( $year = RELEASE_YEAR; $year <= $current_year; $year++ ) {
            array_push($arr, $year);
            $arr[$year] = $year;
        }

        return $arr;
    }

    /*
     * Get timezones in associative array format
     * @return $arr [UTC+0000] => UTC+0000
     */
    public static function getTimezones() {
        $arr        = array();
        $timestamp  = time();

        foreach(timezone_identifiers_list() as $key => $zone) {
            date_default_timezone_set($zone);

            $timezone = 'UTC '.date('P', $timestamp)." ".date('T');
            $arr[$timezone] = $timezone;
        }

        ksort($arr);

        date_default_timezone_set(DEFAULT_TIME_ZONE);

        return $arr;
    }

    /******HYPERLINKS*****/

    /*
     * @param string $module Name of module link will be directing
     * @param mixed $moduleDetails Extra details outlining the depth of the hyperlink (ex: standings, rankings, etc)
     * @param string $subMod Modular options to direct views normally
     * @param string $text Text displaying for hyperlink
     * @param string $textLimit Cut off for hyperlink text
     * @return string $hyperlink Hyperlink html
     */
    public static function generateInternalHyperLink($module, $moduleDetails, $subMod, $text, $textLimit, $link = true) {
        $hyperlink  = '';
        $href       = '';
        $subHref    = '';
        $class      = '';
        
        switch($module) {
            case 'news':
                $href = PAGE_INDEX;
                break;
            case 'servers':
                $href = PAGE_SERVERS . self::cleanLink($text); 
                break;
            case 'standings':
                $subHref = self::generateHyperlinkDetails($module, $moduleDetails, $subMod);

                $href = PAGE_STANDINGS . $subHref . self::cleanLink($moduleDetails->_name);
                break;
            case 'rankings':
                $subHref = self::generateHyperlinkDetails($module, $moduleDetails, $subMod);

                $href = PAGE_RANKINGS . $subHref . self::cleanLink($moduleDetails->_name);
                break;
            case 'guild':
                $class = strtolower($moduleDetails);
                $href = PAGE_GUILD . self::cleanLink($text) . '-_-' . self::cleanLink($subMod);
                break;
            case 'howto':
                $href = PAGE_HOW;
                break;
            case 'privacypolicy':
                $href = PAGE_PRIVACY;
                break;
            case 'tos':
                $href = PAGE_TOS;
                break;
            case 'directory':
                $href = PAGE_DIRECTORY;
                break;
            case 'register':
                $href = PAGE_REGISTER;
                break;
            case 'reset':
                $href = PAGE_RESET_PASSWORD;
                break;
            case 'userpanel':
                $href = PAGE_USER_PANEL;
                break;
        }
        
        if ( $link ) {
            $hyperlink = '<a class="' . $class . '" href="' . $href . '" target="_self">' . self::shortName($text, $textLimit) . '</a>';
        } else {
            $hyperlink = $href;
        }
        
        return $hyperlink;
    }

    public static function generateExternalHyperLink($url, $text, $textLimit, $link = true) {
        $valid_url = parse_url($url);

        if ( !isset($valid_url['scheme']) ) { $url = 'http://' . $url; }

        if ( $link ) {
            $hyperlink = '<a href="' . $url . '" target="_blank">' . $text . '</a>';
        } else {
            $hyperlink = $url;
        }

        return $hyperlink;
    }

    /*
     * @param object $moduleDetails Object containing details used for hyperlink parameters
     * @param string $subMod Sub module option immediately following module in hyperlink address
     * @return string $subHref Extra hyperlink parameters
     */
    public static function generateHyperlinkDetails($module, $moduleDetails, $subMod) {
        $subHref = '';
        
        if ( isset($moduleDetails->_encounterId) ) { // Its Encounter
            $dungeonDetails = CommonDataContainer::$dungeonArray[$moduleDetails->_dungeonId];
            $tierDetails    = CommonDataContainer::$tierArray[$moduleDetails->_tier];
            $subHref        = $subMod . '/' . self::cleanLink($tierDetails->_name) . '/' . self::cleanLink($dungeonDetails->_name) . '/';
        } elseif ( isset($moduleDetails->_dungeonId) ) { // Its Dungeon
            $tierDetails    = CommonDataContainer::$tierArray[$moduleDetails->_tier];
            $subHref        = $subMod . '/' . self::cleanLink($tierDetails->_name) .'/';
        } elseif ( isset($moduleDetails->_tierId) ) { // Its Tier
            $subHref        = $subMod . '/';

            if ( $module == 'rankings' ) {
                $subHref = $subMod . '/';
            }
        } elseif ( isset($moduleDetails->_tierSize) ) { // Its Tier Size
                    
        }

        return $subHref;
    }

    public static function getImageFlag($name, $size = '') {
        $folder = FOLD_FLAGS;
        $image  = '';
        $style  = '';
        $class  = '';
        $name   = strtolower(str_replace(' ', '_', $name));

        if ( $size == 'large' ) { 
            $folder .= 'large/'; 
        } elseif ( $size == 'medium' ) { 
            $folder .= 'medium/';
        } elseif ( $size == 'small' ) {
            $folder .= 'medium/';
            $style  = 'style="height:26px; vertical-align:middle; margin-right: 2px;"';
        } else {
            $class  = 'class="flag-icon"';
        }

        $image  = '<img src="'. $folder . $name . '.png" alt="' . $name . '" ' . $class . ' ' . $style . '>';

        return $image;
    }

    public static function getImageFaction($name, $size = '' ) {
        $image  = '';
        $folder = FOLD_FACTIONS;
        $style  = '';
        $name   = strtolower(str_replace(' ', '_', $name));

        if ( $size == 'large' ) { 
            $folder .= 'large/'; 
        } elseif ( $size == 'medium' ) { 
            $folder .= 'large/';
            //$style  = 'style="height:26px;"';
        }

        $image  = '<img src="'. $folder . $name . '_default.png" alt="' . $name . '" ' . $style . '>';

        return $image;
    }

    public static function getBackgroundFaction($name) {
        $image  = '';
        $folder = FOLD_FACTIONS;
        $name   = strtolower(str_replace(' ', '_', $name));

        $image  = $folder . $name . '_bg.png';

        return $image;
    }

    public static function getTrendImage($trend) {
        $image = "";

        if ( ($trend != "--" || $trend != "NEW") && $trend  > 0 ) $image = $GLOBALS['images']['trend_up'];
        if ( ($trend != "--" || $trend != "NEW") && $trend  < 0 ) $image = $GLOBALS['images']['trend_down'];

        return $image;
    }

    public static function getRankMedal($rank) {
        if ( $rank == 1 ) {
            $rank = $GLOBALS['images']['medal_gold'];
        } elseif ( $rank == 2 ) {
            $rank = $GLOBALS['images']['medal_silver'];
        } elseif ( $rank == 3 ) {
            $rank = $GLOBALS['images']['medal_bronze'];
        }

        return $rank;
    }

    /*****DATE FORMATTING*****/
    public static function formatDate($date, $format) {
        $str_date = strtotime($date);
        return sprintf(date($format, $str_date));
    }

    public static function convertToHoursMins($time) {
        settype($time, 'integer');

        if ( $time < 1 ) { return 'N/A'; }

        $hours   = floor($time / 60);
        $minutes = ($time % 60);

        return sprintf('%d Hours %d Minutes', $hours, $minutes);
    }

    public static function convertToDiffDaysHoursMins($time) {
        settype($time, 'integer');

        if ( $time < 1 ) { return; }

        $days    = floor($time / 86400);
        $hours   = floor(($time - ($days * 86400)) / 3600);
        $minutes = floor(($time - ($days * 86400) - ($hours * 3600))/60);
        $seconds = floor(($time - ($days * 86400) - ($hours * 3600) - ($minutes*60)));

        return sprintf('%d Days %d Hours %d Minutes', $days, $hours, $minutes);
    }

    public static function cleanLink($text) {
        $text = strtolower(str_replace(" ", "_", $text));

        return $text;
    }

    public static function shortName($name, $length) {
        if ( !isset($length) || $length == "" ) { return $name; }

        if ( strlen($name) > $length ) {
            $name = str_replace(" ", "_", $name);

            if ( strrpos($name, "_") == ($length - 1) ) $name = substr($name, 0, $length - 1);
            $name = trim(substr(trim($name), 0, $length))."...";
            $name = str_replace("_", " ", $name);
        }

        return $name;
    }

    public static function convertToOrdinal($number) {
        $abbreviation   = "";
        $ends           = array('th','st','nd','rd','th','th','th','th','th','th');

        if ( $number == "N/A" || $number == "--" ) { return "N/A"; }

        if ( ($number % 100) >= 11 && ($number % 100) <= 13 ) {
            $abbreviation = "{$number}th";
        } else {
            $abbreviation = $number.$ends[$number % 10];
        }

        return $abbreviation;
    }

    public static function formatPoints($points) {
        return number_format($points, 2, '.', ',');
    }

    /*****GET DATA*****/
    public static function getServerByName($serverName) {
        $serverName = str_replace("_", " ", $serverName);

        foreach ( CommonDataContainer::$serverArray as $serverId => $serverDetails ) {
            if ( strcasecmp($serverName, $serverDetails->_name) == 0 ) { return $serverDetails; }
        }
    }

    public static function getTierByName($tierName) {
        $tierName = str_replace("_", " ", $tierName);

        foreach ( CommonDataContainer::$tierArray as $tierId => $tierDetails ) {
            if ( strcasecmp($tierName, $tierDetails->_name) == 0 ) { return $tierDetails; }
        }
    }

    public static function getDungeonByName($dungeonName) {
        $dungeonName = str_replace("_", " ", $dungeonName);

        foreach ( CommonDataContainer::$dungeonArray as $dungeonId => $dungeonDetails ) {
            if ( strcasecmp($dungeonName, $dungeonDetails->_name) == 0 ) { return $dungeonDetails; }
        }
    }

    public static function getEncounterByName($encounterName, $dungeonName) {
        $encounterName = str_replace("_", " ", $encounterName);
        $dungeonName   = str_replace("_", " ", $dungeonName);

        foreach ( CommonDataContainer::$encounterArray as $encounterId => $encounterDetails ) {
            if ( strcasecmp($encounterName, $encounterDetails->_name) == 0 
                 && strcasecmp($dungeonName, $encounterDetails->_dungeon) == 0  ) { 
                return $encounterDetails; 
            }
        }
    }

    public static function getRankSystemByName($identifier) {
        foreach ( CommonDataContainer::$rankSystemArray as $systemId => $systemDetails ) {
            if ( $systemDetails->_abbreviation == $identifier ) {
                return $systemDetails;
            }
        }
    }

    public static function sendTo404($type = '') {
        $pathTo404 = HOST_NAME . '/error';

        if ( !empty($type) ) {
            $pathTo404 .= '/' .$type;
        }

        header('Location: ' . $pathTo404);
        exit;
    }

    public static function validateImage($image) {
        if ( empty($image['tmp_name']) ) { return false; }

        $validImage      = false;
        $validExtensions = unserialize(VALID_IMAGE_FORMATS);
        $imageFileName   = $image['name'];
        $imageFileTemp   = $image['tmp_name'];
        $imageFileType   = exif_imagetype($imageFileTemp);
        $imageFileSize   = $image['size'];
        $imageFileErr    = $image['error'];

        // Checks if Image is a valid format
        $numOfExtensions = count($validExtensions);
        for ( $i = 0; $i < $numOfExtensions; $i++ ) {
            if ( $imageFileType == $validExtensions[$i] ) { $validImage = true; break; }
        }
        
        // Checks if Image is of correct size
        if ( !getimagesize($imageFileTemp) || !($imageFileSize < MAX_IMAGE_SIZE) ) {
            return false;
        }

        // Checks if Image has any errors
        if ( !empty($imageFileErr) ) {
            return false;
        }

        return $validImage;
    }

    public static function sendMail($emailAddress, $emailSubject, $emailMessage, $emailHeaders) {
        return mail($emailAddress, $emailSubject, $emailMessage, $emailHeaders);
    }

    public static function showDialogWindow($dialogOptions) {
        $html = '';
        $html .= '<div id="dialog-wrapper">';
        $html .= '<div id="dialog-title">' . $dialogOptions['title'] . '</div>';
        $html .= '<div id="dialog-body">' . $dialogOptions['message'] . '</div>';
        $html .= '</div>';
        return $html;
    }

    public static function generateDBInsertString($insertString, $encounterDetails, $guildId) {
        // Map Form to encounterDetails mapping

        $encounterObj = new stdClass();
        $encounterObj->_encounterId = self::getEncounterFormValue($encounterDetails, '_encounterId', $guildId);
        $encounterObj->_year        = self::getEncounterFormValue($encounterDetails, '_year', $guildId);
        $encounterObj->_month       = self::getEncounterFormValue($encounterDetails, '_month', $guildId);
        $encounterObj->_day         = self::getEncounterFormValue($encounterDetails, '_day', $guildId);
        $encounterObj->_time        = self::getEncounterFormValue($encounterDetails, '_time', $guildId);
        $encounterObj->_timezone    = self::getEncounterFormValue($encounterDetails, '_timezone', $guildId);
        $encounterObj->_video       = self::getEncounterFormValue($encounterDetails, '_video', $guildId);
        $encounterObj->_serverRank  = self::getEncounterFormValue($encounterDetails, '_serverRank', $guildId);
        $encounterObj->_regionRank  = self::getEncounterFormValue($encounterDetails, '_regionRank', $guildId);
        $encounterObj->_worldRank   = self::getEncounterFormValue($encounterDetails, '_worldRank', $guildId);
        $encounterObj->_countryRank = self::getEncounterFormValue($encounterDetails, '_countryRank', $guildId);
        $encounterObj->_server      = self::getEncounterFormValue($encounterDetails, '_server', $guildId);

        if ( empty($insertString) ) {
                $insertString .= $encounterObj->_encounterId . '||';
                $insertString .= $encounterObj->_year . '-' . $encounterObj->_month . '-' . $encounterObj->_day . '||';
                $insertString .= $encounterObj->_time .'||';
                $insertString .= 'SST' .'||';
                $insertString .= $encounterObj->_video . '||';
                $insertString .= $encounterObj->_serverRank . '||';
                $insertString .= $encounterObj->_regionRank . '||';
                $insertString .= $encounterObj->_worldRank . '||';
                $insertString .= $encounterObj->_countryRank . '||';
                $insertString .= $encounterObj->_server;
        } else {
                $insertString .= '~~';
                $insertString .= $encounterObj->_encounterId . '||';
                $insertString .= $encounterObj->_year . '-' . $encounterObj->_month . '-' . $encounterObj->_day . '||';
                $insertString .= $encounterObj->_time .'||';
                $insertString .= 'SST' .'||';
                $insertString .= $encounterObj->_video . '||';
                $insertString .= $encounterObj->_serverRank . '||';
                $insertString .= $encounterObj->_regionRank . '||';
                $insertString .= $encounterObj->_worldRank . '||';
                $insertString .= $encounterObj->_countryRank . '||';
                $insertString .= $encounterObj->_server;
        }

        return $insertString;
    }

    public static function getEncounterFormValue($encounterDetails, $field, $guildId) {
        $retVal;

        if ( $encounterDetails instanceOf EncounterDetails ) {
            if ( $field == '_server' ) {
                $retVal = CommonDataContainer::$guildArray[$guildId]->_server;
            } else {
                $retVal = $encounterDetails->$field;
            }
        } else {
            if ( $field == '_encounterId' ) { $retVal = $encounterDetails->encounter; }
            if ( $field == '_year' ) { $retVal = $encounterDetails->dateYear; }
            if ( $field == '_month' ) { $retVal = $encounterDetails->dateMonth; }
            if ( $field == '_day' ) { $retVal = $encounterDetails->dateDay; }
            if ( $field == '_time' ) { $retVal = $encounterDetails->dateHour . ':' . $encounterDetails->dateMinute; }
            if ( $field == '_timezone' ) { $retVal = 'SST'; }
            if ( $field == '_video' ) { $retVal = $encounterDetails->video; }
            if ( $field == '_serverRank' ) { $retVal = 0; }
            if ( $field == '_regionRank' ) { $retVal = 0; }
            if ( $field == '_worldRank' ) { $retVal = 0; }
            if ( $field == '_countryRank' ) { $retVal = 0; }
            if ( $field == '_server' ) { $retVal = CommonDataContainer::$guildArray[$guildId]->_server; }
        }

        return $retVal;
    }

    public static function postTwitter($articleTitle, $type) {
        // 0 - Standard News Article
        // 1 - Weekly Report

        $statusUpdate   = '';
        $hyperlinkTitle = strtolower(str_replace(' ', '_', $articleTitle));
        $hyperlinkTitle = strtolower(str_replace('#', 'poundsign', $hyperlinkTitle)); //%23
        $hyperlink      = HOST_NAME . '/news/' . $hyperlinkTitle;
        $hyperlinkShort = self::generateBitlyUrl($hyperlink);

        \Codebird\Codebird::setConsumerKey(TWITTER_KEY, TWITTER_SECRET);
        $cb = \Codebird\Codebird::getInstance();
        $cb->setToken(TWITTER_TOKEN, TWITTER_TOKEN_SECRET);
         
        if ( $type == 0 ) {
            $statusUpdate = 'Latest Article: ' . $articleTitle . ' ' . $hyperlinkShort;
        } else if ( $type == 1 ) {
            $statusUpdate = 'Check out the latest kills in our weekly raiding report! ' . $hyperlinkShort;
        }

        $params = array(
          'status' => $statusUpdate . ' #' . GAME_NAME_1 . ' #mmo #raiding'
        );

        $reply = $cb->statuses_update($params);
    }

    public static function generateBitlyUrl($url) {
        $params                 = array();
        $params['access_token'] = BITLY_TOKEN;
        $params['longUrl']      = $url;
        $results                = bitly_get('shorten', $params);

        return $results['data']['url'];
    }
}

Functions::init();