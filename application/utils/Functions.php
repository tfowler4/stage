<?php

/**
 * commonly used application functions
 */
class Functions {
    /**
     * initialize
     * 
     * @return void
     */
    public static function init() {
        CommonDataContainer::$daysArray      = self::getDays();
        CommonDataContainer::$monthsArray    = self::getMonths();
        CommonDataContainer::$yearsArray     = self::getYears();
        CommonDataContainer::$hoursArray     = self::getHours();
        CommonDataContainer::$minutesArray   = self::getMinutes();
        CommonDataContainer::$timezonesArray = self::getTimezones();
    }

    /**
     * get minutes in array format with leading zeros
     * 
     * @return array [ [0] => 00, [59] => 59 ]
     */
    public static function getMinutes() {
        $retArray = array();

        $retArray['00'] = '00';

        for ( $minute = 1; $minute < 60; $minute++ ) {
            if ( strlen($minute) == 1 ) { $minute = '0' . $minute; }
            
            $retArray[$minute] = $minute;
        }

        return $retArray;
    }

    /**
     * get hours in array format in 24-hour format with leading zeros
     * 
     * @return array [ [0] => 00 (Midnight), [23] => 23 (11pm) ]
     */
    public static function getHours() {
        $retArray = array();

        $retArray['00'] = '12am';

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

            $retArray[$hour] = $hourValue;
        }

        return $retArray;
    }

    /**
     * get months in array format with leading zeros
     * 
     * @return array [ [1] => 1 (January), [12] => 12 (December) ]
     */
    public static function getMonths() {
        $retArray = array();

        for ( $month = 1; $month < 13; $month++ ) {
            $monthValue = $month;

            if ( $month < 10 ) { $month = '0' . $month; }

            $retArray[$month] = date('F', mktime(0, 0, 0, $month, 1, date('Y'))) . ' - ' . $month;
        }

        return $retArray;
    }

    /**
     * get days in array format with leading zeros
     * 
     * @return array [ [1] => 1, [32] => 12 ]
     */
    public static function getDays() {
        $retArray = array();

        for ( $day = 1; $day < 33; $day++ ) {
            if ( strlen($day) == 1 ) { $day = '0' . $day; }
            $retArray[$day] = $day;
        }

        return $retArray;
    }

    /**
     * get years in array format from release year to current year
     * 
     * @return array [ [0] => 2011, [5] => 2016 ] 
     */ 
    public static function getYears() {
        $retArray    = array();
        $currentYear = date('Y');

        for ( $year = RELEASE_YEAR; $year <= $currentYear; $year++ ) {
            $retArray[$year] = $year;
        }

        return $retArray;
    }

    /**
     * get timezones in associative array format
     * 
     * @return array [ "UTC+0000" => UTC+0000 ]
     */
    public static function getTimezones() {
        $retArray  = array();
        $timestamp = time();

        foreach(timezone_identifiers_list() as $key => $zone) {
            date_default_timezone_set($zone);

            $timezone            = 'UTC ' . date('P', $timestamp) . ' ' . date('T');
            $retArray[$timezone] = $timezone;
        }

        ksort($retArray);

        date_default_timezone_set(DEFAULT_TIME_ZONE);

        return $retArray;
    }

    /**
     * generate hyperlink that directs to another location on the website
     * 
     * @param string $module       [ name of module link will be directing ]
     * @param mixed $moduleDetails [ extra details outlining the depth of the hyperlink (ex: standings, rankings, etc) ]
     * @param string $subMod       [ modular options to direct views normally ]
     * @param string $text         [ text displaying for hyperlink ]
     * @param string $textLimit    [ cut off for hyperlink text ]
     * 
     * @return string [ hyperlink html ]
     */
    public static function generateInternalHyperLink($module, $moduleDetails, $subMod, $text, $textLimit, $link = true) {
        $hyperlink  = '';
        $href       = '';
        $subHref    = '';
        $class      = '';
        
        switch($module) {
            case 'news':
                $href = PAGE_INDEX;

                if ( !empty($moduleDetails) ) {
                    $href .= 'news/' . self::cleanLink($moduleDetails);
                }
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
                $class = 'class="' . strtolower($moduleDetails) . '"';
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
            $hyperlink = '<a ' . $class . ' href="' . $href . '" target="_self">' . self::shortName($text, $textLimit) . '</a>';
        } else {
            $hyperlink = $href;
        }
        
        return $hyperlink;
    }

    /**
     * generate hyperlink to redirect off the website
     * 
     * @param  string  $url       [ url string ]
     * @param  string  $text      [ display text ]
     * @param  integer $textLimit [ number of characters ]
     * @param  boolean $link      [ true if html else url only ]
     * 
     * @return string [ hyperlink html ]
     */
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

    /**
     *
     * 
     * @param object $moduleDetails [ object containing details used for hyperlink parameters ]
     * @param string $subMod        [ sub module option immediately following module in hyperlink address ]
     * 
     * @return string [ extra hyperlink parameters ]
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
        }

        return $subHref;
    }

    /**
     * get country flag image
     * 
     * @param  string $name [ name of country ]
     * @param  string $size [ size of image ]
     * 
     * @return string [ html containing country flag image ]
     */
    public static function getImageFlag($name, $size = '') {
        $folder = FOLD_FLAGS;
        $image  = '';
        $class  = '';
        $name   = strtolower(str_replace(' ', '_', $name));

        if ( $size == 'large' ) { 
            $folder .= 'large/'; 
            $class = 'class="large-flag"';
        } elseif ( $size == 'medium' ) { 
            $folder .= 'medium/';
            $class = 'class="flag-icon-med"';
        } elseif ( $size == 'small' ) {
            $folder .= 'medium/';
            $class  = 'class="flag-custom"';
        } else {
            $class  = 'class="flag-icon hidden-xs"';
        }

        $image  = '<img src="'. $folder . $name . '.png" alt="' . $name . '" ' . $class . '>';

        return $image;
    }

    /**
     * get faction logo image
     * 
     * @param  atring $name [ faction name ]
     * @param  string $size [ size of image ]
     * 
     * @return string [ html containing faction image ]
     */
    public static function getImageFaction($name, $size = '' ) {
        $image  = '';
        $folder = FOLD_FACTIONS;
        $name   = strtolower(str_replace(' ', '_', $name));

        if ( $size == 'large' ) { 
            $folder .= 'large/'; 
        } elseif ( $size == 'medium' ) { 
            $folder .= 'large/';
        }

        $image = '<img src="'. $folder . $name . '_default.png" alt="' . $name . '">';

        return $image;
    }

    /**
     * get trending arrow image
     * 
     * @param  string $trend [ guild trend ]
     * 
     * @return string [ html containing trending arrow image ]
     */
    public static function getTrendImage($trend) {
        $image = '';

        if ( ($trend != '--' || $trend != 'NEW') && $trend  > 0 ) { $image = IMG_ARROW_TREND_UP_SML; }
        if ( ($trend != '--' || $trend != 'NEW') && $trend  < 0 ) { $image = IMG_ARROW_TREND_DOWN_SML; }

        return $image;
    }

    /**
     * get rank medal image
     * 
     * @param  string $rank [ guild rank ]
     * 
     * @return string [ html containing rank medal image ]
     */
    public static function getRankMedal($rank) {
        if ( $rank == 1 ) {
            $rank = IMG_MEDAL_GOLD;
        } elseif ( $rank == 2 ) {
            $rank = IMG_MEDAL_SILVER;
        } elseif ( $rank == 3 ) {
            $rank = IMG_MEDAL_BRONZE;
        }

        return $rank;
    }

    /**
     * format date into provided format
     * 
     * @param  string $date   [ date string ]
     * @param  string $format [ date format ]
     * 
     * @return string [ formated date string ]
     */
    public static function formatDate($date, $format) {
        $str_date = strtotime($date);
        return sprintf(date($format, $str_date));
    }

    /**
     * convert unix timestamp to hours minutes format
     * 
     * @param  string $time [ unix timestamp ]
     * 
     * @return string [ time in hours minutes format ]
     */
    public static function convertToHoursMins($time) {
        settype($time, 'integer');

        if ( $time < 1 ) { return 'N/A'; }

        $hours   = floor($time / 60);
        $minutes = ($time % 60);

        return sprintf('%d Hours %d Minutes', $hours, $minutes);
    }

    /**
     * convert unix timestamp to time difference format
     * 
     * @param  string $time [ unix format time ]
     * 
     * @return string [ time difference ]
     */
    public static function convertToDiffDaysHoursMins($time) {
        settype($time, 'integer');

        if ( $time < 1 ) { return; }

        $days    = floor($time / 86400);
        $hours   = floor(($time - ($days * 86400)) / 3600);
        $minutes = floor(($time - ($days * 86400) - ($hours * 3600))/60);
        $seconds = floor(($time - ($days * 86400) - ($hours * 3600) - ($minutes*60)));

        return sprintf('%d Days %d Hours %d Minutes', $days, $hours, $minutes);
    }

    /**
     * remove spaces and replace with underscores
     * 
     * @param  string $text [ text to be modified]
     * 
     * @return string [ modified text ]
     */
    public static function cleanLink($text) {
        $text = strtolower(str_replace(' ', '_', $text));

        return $text;
    }

    /**
     * apply limit to text characters
     * 
     * @param  string  $name   [ text string ]
     * @param  integer $length [ text length ]
     * 
     * @return string [ text shortened ]
     */
    public static function shortName($name, $length) {
        if ( !isset($length) || $length == '' ) { return $name; }

        if ( strlen($name) > $length ) {
            $name = str_replace(' ', '_', $name);

            if ( strrpos($name, '_') == ($length - 1) ) { $name = substr($name, 0, $length - 1); }
            $name = trim(substr(trim($name), 0, $length));
            $name = str_replace('_', ' ', $name);

            $lastChar = substr($name, strlen($name) -1);

            if ( $lastChar == ' ' ) {
                $name = substr($name, 0, strlen($name) - 1);
            }

            $name .= '...';
        }

        return $name;
    }

    /**
     * convert number to ordinal format
     * 
     * @param  integer $number [ number in need to be converted ]
     * 
     * @return string [ number in ordinal format ]
     */
    public static function convertToOrdinal($number) {
        $abbreviation = "";
        $ends         = array('th','st','nd','rd','th','th','th','th','th','th');

        if ( $number == 'N/A' || $number == '--' ) { return 'N/A'; }

        if ( ($number % 100) >= 11 && ($number % 100) <= 13 ) {
            $abbreviation = $number . 'th';
        } else {
            $abbreviation = $number . $ends[$number % 10];
        }

        return $abbreviation;
    }

    /**
     * formats ranking points
     * 
     * @param  string $points [ ranking points ]
     * 
     * @return string [ points in 2 decimal format with thousand separator ]
     */
    public static function formatPoints($points) {
        return number_format($points, 2, '.', ',');
    }

    /**
     * get server data object by name
     * 
     * @param  string $serverName [ name of server ]
     * 
     * @return Server [ server data object ]
     */
    public static function getServerByName($serverName) {
        $serverName = str_replace('_', ' ', $serverName);

        foreach ( CommonDataContainer::$serverArray as $serverId => $serverDetails ) {
            if ( strcasecmp($serverName, $serverDetails->_name) == 0 ) { return $serverDetails; }
        }
    }

    /**
     * get tier data object by name
     * 
     * @param  string $tierName [ name of tier ]
     * 
     * @return Tier [ tier data object ]
     */
    public static function getTierByName($tierName) {
        $tierName = str_replace('_', ' ', $tierName);

        foreach ( CommonDataContainer::$tierArray as $tierId => $tierDetails ) {
            if ( strcasecmp($tierName, $tierDetails->_name) == 0 ) { return $tierDetails; }
        }
    }

    /**
     * get dungeon data object by name
     * 
     * @param  string $dungeonName [ name of dungeon ]
     * 
     * @return Dungeon [ dungeon data object ]
     */
    public static function getDungeonByName($dungeonName) {
        $dungeonName = str_replace('_', ' ', $dungeonName);

        foreach ( CommonDataContainer::$dungeonArray as $dungeonId => $dungeonDetails ) {
            if ( strcasecmp($dungeonName, $dungeonDetails->_name) == 0 ) { return $dungeonDetails; }
        }
    }

    /**
     * get encounter data object by name
     * 
     * @param  string $encounterName [ name of encounter ]
     * @param  string $dungeonName   [ name of dungeon ]
     * 
     * @return Encounter [ encounter data object ]
     */
    public static function getEncounterByName($encounterName, $dungeonName) {
        $encounterName = str_replace('_', ' ', $encounterName);
        $dungeonName   = str_replace('_', ' ', $dungeonName);

        foreach ( CommonDataContainer::$encounterArray as $encounterId => $encounterDetails ) {
            if ( strcasecmp($encounterName, $encounterDetails->_name) == 0 
                 && strcasecmp($dungeonName, $encounterDetails->_dungeon) == 0  ) { 
                return $encounterDetails; 
            }
        }
    }

    /**
     * get rank system data object by abbreviation
     * 
     * @param  string $identifier [ rank system abbreviation]
     * 
     * @return RankSystem [ rank system object ]
     */
    public static function getRankSystemByName($identifier) {
        foreach ( CommonDataContainer::$rankSystemArray as $rankSystemId => $rankSystemDetails ) {
            if ( $rankSystemDetails->_abbreviation == $identifier ) {
                return $rankSystemDetails;
            }
        }
    }

    /**
     * direct page to 404 not found page
     * 
     * @param  integer $type [ type of error page code ]
     * 
     * @return void
     */
    public static function sendTo404($type = '') {
        $pathTo404 = HOST_NAME . '/error';

        if ( !empty($type) ) {
            $pathTo404 .= '/' .$type;
        }

        header('Location: ' . $pathTo404);
        exit;
    }

    /**
     * validate image submitted from form to ensure format and size are valid
     * 
     * @param  array [ image from form as array ]
     * 
     * @return boolean [ true if image is valid ]
     */
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

    /**
     * send email address
     * 
     * @param  string $emailAddress [ address email being sent to ]
     * @param  string $emailSubject [ subject of email ]
     * @param  string $emailMessage [ email message content ]
     * @param  string $emailHeaders [ additional email headers ]
     * 
     * @return boolean [ true if mail is sent correctly ]
     */
    public static function sendMail($emailAddress, $emailSubject, $emailMessage) {
        $headers  = "From: " . $emailAddress . "\r\n";
        $headers .= "Reply-To: ". $emailAddress . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $htmlMessage  = '<html>';
        $htmlMessage .= '<body>';
        $htmlMessage .= $emailMessage;
        $htmlMessage .= '</body>';
        $htmlMessage .= '</html>';

        return mail($emailAddress, $emailSubject, $htmlMessage, $headers);
    }

    /**
     * display message dialog window
     * 
     * @param  array $dialogOptions [ array with dialog options ]
     * 
     * @return string [ html string containing dialog window ]
     */
    public static function showDialogWindow($dialogOptions) {
        $html = '';
        $html .= '<div>';
        $html .= '<div>' . $dialogOptions['title'] . '</div>';
        $html .= '<div>' . $dialogOptions['message'] . '</div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * post news article to twitter social network
     * 
     * @param  string  $articleTitle [ title of news article ]
     * @param  integer $type         [ type of news article ]
     * 
     * @return void
     */
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

    /**
     * create shortened url strings using bitly api
     * 
     * @param  string $url [ html string to be shortned ]
     * 
     * @return string [ url shortened by bitly ]
     */
    public static function generateBitlyUrl($url) {
        $params                 = array();
        $params['access_token'] = BITLY_TOKEN;
        $params['longUrl']      = $url;
        $results                = bitly_get('shorten', $params);

        return $results['data']['url'];
    }

    /**
     * get the difference in point amounts between two point values
     * 
     * @param  double $currentPoints [ starting points value ]
     * @param  double $newPoints     [ new points value ]
     * 
     * @return integer [ difference in points ]
     */
    public static function getPointDiff($currentPoints, $newPoints) {
        $pointDiff = $newPoints - $currentPoints;

        if ( $currentPoints == 0 ) { 
            return '--'; 
        }

        return number_format($pointDiff, 2, '.', ',');
    }

    /**
     * generate all encounter standings and rankings information
     *      * 
     * @return void
     */
    public static function getAllGuildDetails($guildDetails) {
        $dbh = DbFactory::getDbh();

        $guildDetails->generateRankDetails('encounters');

        $query = $dbh->prepare(sprintf(
            "SELECT kill_id,
                    guild_id,
                    encounter_id,
                    dungeon_id,
                    tier,
                    raid_size,
                    datetime,
                    date,
                    time,
                    time_zone,
                    server,
                    videos,
                    server_rank,
                    region_rank,
                    world_rank,
                    country_rank
               FROM %s
              WHERE guild_id=%d", 
                    DbFactory::TABLE_KILLS, 
                    $guildDetails->_guildId
                ));
        $query->execute();

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $encounterId         = $row['encounter_id'];
            $encounterDetails    = CommonDataContainer::$encounterArray[$encounterId];
            $dungeonId           = $encounterDetails->_dungeonId;
            $dungeonDetails      = CommonDataContainer::$dungeonArray[$dungeonId];
            $tierId              = $dungeonDetails->_tier;
            $tierDetails         = CommonDataContainer::$tierArray[$tierId];

            $arr = $guildDetails->_progression;
            $arr['dungeon'][$dungeonId][$encounterId] = $row;
            $arr['encounter'][$encounterId] = $row;
            $guildDetails->_progression = $arr;
        }

        $guildDetails->generateEncounterDetails('');

        return $guildDetails;
    }

    /**
     * update user details in database to new selected template
     *
     * @param  string $templateName [ name of template skin ]
     * @param  User   $userDetails  [ User Details Object ]
     * 
     * @return void
     */
    public static function updateUserTemplate($templateName, $userDetails) {
        $dbh = DbFactory::getDbh();

        $query = $dbh->prepare(sprintf(
            "UPDATE %s
                SET default_template = '%s'
              WHERE user_id = '%s'",
            DbFactory::TABLE_USERS,
            $templateName,
            $userDetails->_userId
        ));
        $query->execute();
    }
}

Functions::init();