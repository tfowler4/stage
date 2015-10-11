<?php

/**
 * kill administration
 */
class AdministratorModelKill {
    protected $_action;
    protected $_dbh;
    protected $_formFields;

    /**
     * constructor
     */
    public function __construct($action, $dbh) {
        $this->_action = $action;
        $this->_dbh    = $dbh;

        if ( Post::get('adminpanel-kill-guild-id') || Post::get('submit') ) {
            $this->populateFormFields();

            switch ($this->_action) {
                case "add":
                    $this->addNewKill();
                    break;
                case "edit":
                    $this->editKill(Post::get('adminpanel-kill-guild-id'));
                    break;
                case "remove":
                    $this->removeKill(Post::get('adminpanel-kill-guild-id'));
                    break;
            }
        }

        die;
    }

    /**
     * populates form field object with data from form
     * 
     * @return void
     */
    public function populateFormFields() {
        $this->_formFields = new AdminKillFormFields();

        $this->_formFields->killId     = Post::get('adminpanel-kill-id');
        $this->_formFields->guildId    = Post::get('adminpanel-kill-guild-id');
        $this->_formFields->encounter  = Post::get('adminpanel-kill-encounter-id');
        $this->_formFields->dateMonth  = Post::get('adminpanel-kill-month');
        $this->_formFields->dateDay    = Post::get('adminpanel-kill-day');
        $this->_formFields->dateYear   = Post::get('adminpanel-kill-year');
        $this->_formFields->dateHour   = Post::get('adminpanel-kill-hour');
        $this->_formFields->dateMinute = Post::get('adminpanel-kill-minute');
        $this->_formFields->screenshot = Post::get('adminpanel-screenshot');
        $this->_formFields->videoId    = Post::get('video-link-id');
        $this->_formFields->videoTitle = Post::get('video-link-title');
        $this->_formFields->videoUrl   = Post::get('video-link-url');
        $this->_formFields->videoType  = Post::get('video-link-type');
    }

    /**
     * insert new kill details in encounterkills_table
     *
     * @return void
     */
    public function addNewKill() {
        DBObjects::addKill($this->_formFields);

        if ( !empty($this->_formFields->screenshot['tmp_name']) ) {
            $imagePath = ABS_FOLD_KILLSHOTS . $this->_formFields->guildId . '-' . $this->_formFields->encounter;

            if ( file_exists($imagePath) ) {
                unlink($imagePath);
            }

            move_uploaded_file($this->_formFields->screenshot['tmp_name'], $imagePath);
        }
    }

    /**
     * create html to prepare form and display guild and encounter name
     * 
     * @param  GuildDetails $guildDetails [ guild details object ]
     * 
     * @return string [ return html containing specified dungeon details ]
     */
    public function editKillSelectHtml($guildDetails) {
        $html = '';
        $html .= '<form class="admin-form kill edit" id="form-kill-edit" method="POST" action="' . PAGE_ADMIN . '">';
        $html .= '<table class="admin-edit-kill-listing">';
        $html .= '<tr><th>Encounter Name</th></tr>';
        $html .= '<tr><td><input hidden type="text" id="edit-kill-guild-id" name="adminpanel-kill-guild-id" value="' . $guildDetails->_guildId . '"/><select id="kill-edit-encounter" name="adminpanel-kill-encounter-id">';
        $html .= '<option value="">Select Encounter</option>';
            foreach ( (array)$guildDetails->_encounterDetails as $encounterId => $encounterDetails ):
                if ( isset($encounterDetails->_encounterId) ):
                    $html .= '<option value="' . $encounterDetails->_encounterId . '">' . $encounterDetails->_dungeon . '-' . $encounterDetails->_encounterName . '</option>';
                endif;
            endforeach;
        $html .= '</select></td></tr>';
        $html .= '</table>';
        $html .='</form>';

        return $html;
    }

    /**
     * create html to prepare form and display guild encounter kill details
     * 
     * @param  EncounterDetails $encounterDetails [ encounter details object ]
     * 
     * @return string [ return html containing specified dungeon details ]
     */
    public function editKillDetailsHtml($encounterDetails) {
        $videoArray = $encounterDetails['videos'];

        $date = explode('-', $encounterDetails['date']);
        $time = explode(':', $encounterDetails['time']);

        $videoTypeArray      = array();
        $videoTypeArray['0'] = 'General Kill';
        $videoTypeArray['1'] = 'Encounter Guide';

        $html = '';
        $html .= '<form class="admin-form kill edit" id="form-kill-edit-details" method="POST" action="' . PAGE_ADMIN . '">';
        $html .= '<table class="admin-edit-kill-listing">';
        $html .= '<input hidden type="text" id="edit-kill-id" name="adminpanel-kill-id" value="' . $encounterDetails['kill_id'] . '"/>';
        $html .= '<input hidden type="text" id="edit-kill-guild-id" name="adminpanel-kill-guild-id" value="' . $encounterDetails['guild_id'] . '"/>';
        $html .= '<input hidden type="text" id="edit-kill-encounter-id" name="adminpanel-kill-encounter-id" value="' . $encounterDetails['encounter_id'] . '"/>';
        $html .= '<tr><th>Date</th></tr>';
        $html .= '<tr><td><select class="admin-select month" name="adminpanel-kill-month">';
            foreach( CommonDataContainer::$monthsArray as $month => $monthValue) {
                if ( $month == $date[1] ) {
                    $html .= '<option value="' . $month . '" selected>' . $monthValue . '</option>';
                } else {
                    $html .='<option value="' . $month . '">' . $monthValue . '</option>';
                }
            }
        $html .= '</select>';
        $html .= '<select class="admin-select day" name="adminpanel-kill-day">';
            foreach( CommonDataContainer::$daysArray as $day => $dayValue ) {
                if ( $day == $date[2] ) {
                    $html .= '<option value="' . $day . '" selected>' . $dayValue . '</option>';
                } else {
                    $html .='<option value="' . $day . '">' . $dayValue . '</option>';
                }
            }
        $html .= '</select>';
        $html .= '<select class="admin-select year" name="adminpanel-kill-year">';
            foreach( CommonDataContainer::$yearsArray as $year => $yearValue ) {
                if ( $year == $date[0] ) {
                    $html .= '<option value="' . $year . '" selected>' . $yearValue . '</option>';
                } else {
                    $html .= '<option value="' . $year . '">' . $yearValue . '</option>';
                }
            }
        $html .= '</select></td></tr>';
        $html .= '<tr><th>Time</th></tr>';
        $html .= '<tr><td><select class="admin-select hour" name="adminpanel-kill-hour">';
            foreach( CommonDataContainer::$hoursArray as $hour => $hourValue ) {
                if ( $hour == $time[0] ) {
                    $html .= '<option value="' .$hour . '" selected>' . $hourValue .'</option>';
                } else {
                    $html .= '<option value="' .$hour . '">' . $hourValue .'</option>';
                }
            }
        $html .= '</select>';
        $html .= '<select class="admin-select minute" name="adminpanel-kill-minute">';
            foreach( CommonDataContainer::$minutesArray as $minute => $minuteValue ) {
                if ( $minute == $time[1] ) {
                    $html .= '<option value="' .$minute . '" selected>' .$minuteValue . '</option>';
                } else {
                    $html .= '<option value="' .$minute . '">' .$minuteValue . '</option>';
                }
            }
        $html .= '</select></td></tr>';
        $html .= '<tr><th>Screenshot</th></tr>';
        $html .= '<tr><td><input type="file" name="adminpanel-screenshot" /></td></tr>';
        $html .= '<tr><th>Kill Video</th></tr>';
        $html .= '<tr><td>';
        $html .= '<div class="video-link-container">';
            foreach ( $videoArray as $videoId => $videoDetails ) {
                if ( !empty($videoDetails['url']) ) {
                    $html .= '<div class="video-link-wrapper">';
                    $html .= 'Video #' . $videoId . '<br>';
                    $html .= '<input  id="user-form-video-id-' . $videoId . '" type="hidden" name="video-link-id[]" value="' . $videoId . '" />';
                    $html .= '<div>';
                    $html .= '<label class="video-link-label">Notes: </label>';
                    $html .= '<input id="user-form-video-title-' . $videoId . '" type="text" name="video-link-title[]" class="width-200"  value="' . $videoDetails['notes'] . '" />';
                    $html .= '</div>';
                    $html .= '<div>';
                    $html .= '<label class="video-link-label">URL: </label>';
                    $html .= '<input id="user-form-video-url-' . $videoId . '" type="text" name="video-link-url[]" class="width-200"  value="' . $videoDetails['url'] . '" />';
                    $html .= '</div>';
                    $html .= '<div>';
                    $html .= '<label class="video-link-label">Type: </label>';
                    $html .= '<select id="user-form-video-type-' . $videoId . '" name="video-link-type[]" class="width-200">';

                    foreach( $videoTypeArray as $videoType => $videoTypeValue ) {
                        if ( $videoType == $videoDetails['type'] ) {
                            $html .= '<option value="' . $videoType . '" selected>' . $videoTypeValue . '</option>';
                        } else {
                            $html .= '<option value="' . $videoType . '">' . $videoTypeValue . '</option>';
                        }
                    }

                    $html .= '</select>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
            }
        $html .= '</div>';
        $html .= '<a class="new-video-link" href="#">Add New Video</a>';
        $html .= '</td></tr>';
        $html .= '</table>';
        $html .= '<div class="vertical-separator"></div>';
        $html .= '<input id="admin-submit-tier-edit-action" type="hidden" name="submit" value="submit" />';
        $html .= '<input id="admin-submit-kill-edit" type="submit" value="Submit" />';
        $html .='</form>';

        return $html;
    }

    /**
     * delete from encounterkills_table by specified id
     * 
     * @param string $guildId [ id of a specific guild]
     *
     * @return void
     */
    public function editKill($guildId) {
        $guildDetails = CommonDataContainer::$guildArray[$guildId];
        $this->getAllGuildDetails($guildDetails);

        $html = '';

        if ( Post::get('submit') ) {
            DBObjects::editKill($this->_formFields);

            if ( !empty($this->_formFields->screenshot['tmp_name']) ) {
                $imagePath = ABS_FOLD_KILLSHOTS . $this->_formFields->guildId . '-' . $this->_formFields->encounter;

                if ( file_exists($imagePath) ) {
                    unlink($imagePath);
                }

                move_uploaded_file($this->_formFields->screenshot['tmp_name'], $imagePath);

                $this->_guildDetails = $this->_getUpdatedGuildDetails($this->_guildDetails->_guildId);
            }
        } elseif ( !Post::get('adminpanel-kill-encounter-id') ) {
            $html = $this->editKillSelectHtml($guildDetails);

            echo $html;
        } elseif ( Post::get('adminpanel-kill-encounter-id') ) {
            $encounterId = Post::get('adminpanel-kill-encounter-id');

            $html = $this->getEncounter($guildId, $encounterId);

            echo $html;
        }
    }

    /**
     * create html to prepare form and display guild and encounter name
     * 
     * @param  GuildDetails $guildDetails [ guild details object ]
     * 
     * @return string [ return html containing specified dungeon details ]
     */
    public function removeKillHtml($guildDetails) {
        $html = '';
        $html .= '<form class="admin-form kill remove" id="form-kill-remove" method="POST" action="' . PAGE_ADMIN . '">';
        $html .= '<table class="admin-remove-kill-listing">';
        $html .= '<tr><th>Encounter Name</th></tr>';
        $html .= '<tr><td><input hidden type="text" name="adminpanel-kill-guild-id" value="' . $guildDetails->_guildId . '"/><select name="adminpanel-kill-encounter-id">';
        $html .= '<option value="">Select Encounter</option>';
            foreach ( (array)$guildDetails->_encounterDetails as $encounterId => $encounterDetails ):
                if ( isset($encounterDetails->_encounterId) ):
                    $html .= '<option value="' . $encounterDetails->_encounterId . '">' . $encounterDetails->_dungeon . '-' . $encounterDetails->_encounterName . '</option>';
                endif;
            endforeach;
        $html .= '</select></td></tr>';
        $html .= '</table>';
        $html .= '<div class="vertical-separator"></div>';
        $html .= '<input id="admin-submit-tier-edit-action" type="hidden" name="submit" value="submit" />';
        $html .= '<input id="admin-submit-kill-remove" type="submit" value="Remove" />';
        $html .='</form>';

        return $html;
    }

    /**
     * delete from encounterkills_table by specified id
     * 
     * @return void
     */
    public function removeKill($guildId) {
        // if the submit field is present, remove kill data
        if ( Post::get('submit') ) {
            DBObjects::removeKill($this->_formFields);

            $this->_removeScreenshot($this->_formFields->guildId, $this->_formFields->encounter);
        } else {
            $html         = '';
            $guildDetails = CommonDataContainer::$guildArray[$this->_formFields->guildId];

            $this->getAllGuildDetails($guildDetails);

            $html = $this->removeKillHtml($guildDetails);

            echo $html;
        }
    }

    /**
     * generate all encounter standings and rankings information
     * 
     * @return void
     */
    public function getAllGuildDetails($guildDetails) {
        $guildDetails->generateRankDetails('encounters');

        $dbh       = DbFactory::getDbh();
        $dataArray = array();

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
    }

    /**
     * To get encounter kill details
     * 
     * @param  string $guildId     [ id of a specific guild ]
     * @param  string $encounterId [ id of a specific encounter ]
     * 
     * @return void
     */
    public function getEncounter($guildId, $encounterId) {
        $videoArray = array();
        $html = '';

            $query = $this->_dbh->query(sprintf(
                "SELECT video_id,
                        guild_id,
                        encounter_id,
                        url,
                        type,
                        notes
                   FROM %s
                  WHERE guild_id = '%s'
                    AND encounter_id = '%s'",
                DbFactory::TABLE_VIDEOS,
                $guildId,
                $encounterId
            ));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $videoId = $row['video_id'];
                $videoArray[$videoId] = $row;
            }

            $query = $this->_dbh->query(sprintf(
                "SELECT kill_id,
                        guild_id,
                        encounter_id,
                        date,
                        time
                   FROM %s
                  WHERE guild_id = '%s'
                    AND encounter_id = '%s'",
                DbFactory::TABLE_KILLS,
                $guildId,
                $encounterId
            ));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $row['videos'] = $videoArray;
                $html = $this->editKillDetailsHtml($row);
            }

        echo $html;
    }

    /**
     * remove screenshot image from filesystem
     * 
     * @return void
     */
    private function _removeScreenshot($guildId, $encounterId) {
        $imagePath = ABS_FOLD_KILLSHOTS . $guildId . '-' . $encounterId;

        if ( file_exists($imagePath) ) {
            unlink($imagePath);
        }
    }
}