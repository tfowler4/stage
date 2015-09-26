<?php
class UserPanelModelKill extends UserPanelModel {
    protected $_action;
    protected $_formFields;
    protected $_dialogOptions;
    protected $_guildDetails;
    protected $_encounterDetails;
    protected $_encounterScreenshot;

    const KILLS_ADD       = 'add';
    const KILLS_REMOVE    = 'remove';
    const KILLS_EDIT      = 'edit';

    const TABLE_HEADER_PROGRESSION = array(
            'Encounter'      => '_encounterName',
            'Date Completed' => '_datetime',
            'Server'         => '_serverLink',
            'WR'             => '_worldRankImage',
            'RR'             => '_regionRankImage',
            'SR'             => '_serverRankImage',
            'Kill Video'     => '_videoLink',
            'Screenshot'     => '_screenshotLink',
            'Options'        => '_options'
        );

    public function __construct($action, $formFields, $guildDetails, $encounterDetails) {
        $this->_guildDetails     = $guildDetails;
        $this->_encounterDetails = $encounterDetails;
        $this->_action           = $action;
        $this->_formFields       = $formFields;

        if ( Post::formActive() ) {
            $this->_processKillForm();

            if ( $this->_validForm ) {
                switch($this->_action) {
                    case self::KILLS_ADD:
                        $this->_addKill();
                        break;
                    case self::KILLS_REMOVE:
                        $this->_removeKill();
                        break;
                    case self::KILLS_EDIT:
                        $this->_editKill();
                        break;
                }
            }
        }

        $this->mergeOptionsToEncounters();
    }

    /**
     * process submitted kill submitted form
     * 
     * @return void
     */
    private function _processKillForm() {
        $this->_formFields->guildId    = Post::get('userpanel-guild');
        $this->_formFields->encounter  = Post::get('userpanel-encounter');
        $this->_formFields->dateMonth  = Post::get('userpanel-month');
        $this->_formFields->dateDay    = Post::get('userpanel-day');
        $this->_formFields->dateYear   = Post::get('userpanel-year');
        $this->_formFields->dateHour   = Post::get('userpanel-hour');
        $this->_formFields->dateMinute = Post::get('userpanel-minute');
        $this->_formFields->screenshot = Post::get('userpanel-screenshot');
        $this->_formFields->video      = Post::get('userpanel-video');
        $this->_formFields->videoId    = Post::get('video-link-id');
        $this->_formFields->videoTitle = Post::get('video-link-title');
        $this->_formFields->videoUrl   = Post::get('video-link-url');
        $this->_formFields->videoType  = Post::get('video-link-type');

        if ( !empty($this->_formFields->guildId)
             && !empty($this->_formFields->encounter) 
             && !empty($this->_formFields->dateMonth) 
             && !empty($this->_formFields->dateDay) 
             && !empty($this->_formFields->dateYear) 
             && !empty($this->_formFields->dateHour) 
             && !empty($this->_formFields->dateMinute) 
             && !empty($this->_formFields->screenshot) ) {
                $this->_validForm = true;
        }

        if ( $this->_action == self::KILLS_REMOVE ) {
            if ( !empty($this->_formFields->guildId)
                 && !empty($this->_formFields->encounter ) ) {
                    $this->_validForm = true;
            }
        }
    }

    /**
     * remove kill from encounterkills table in database
     * 
     * @return void
     */
    private function _removeKill() {
        DBObjects::removeKill($this->_formFields);

        $this->_removeScreenshot($this->_formFields->guildId, $this->_formFields->encounter);

        $this->_dialogOptions = array('title' => 'Success', 'message' => 'Your kill has been removed successfully!');
    }

    /**
     * edit kill from encounterkills table in database
     * 
     * @return void
     */
    private function _editKill() {
        DBObjects::editKill($this->_formFields);

        if ( Functions::validateImage($this->_formFields->screenshot) ) {
            $imagePath = ABS_FOLD_KILLSHOTS . $this->_formFields->guildId . '-' . $this->_formFields->encounter;

            if ( file_exists($imagePath) ) {
                unlink($imagePath);
            }

            move_uploaded_file($this->_formFields->screenshot['tmp_name'], $imagePath);

            $this->_guildDetails = $this->_getUpdatedGuildDetails($this->_guildDetails->_guildId);
        }

        $this->_encounterDetails = $this->_getUpdatedEncounterDetails($this->_guildDetails->_guildId, $this->_encounterDetails->_encounterId);

        $this->_dialogOptions = array('title' => 'Success', 'message' => 'Your kill has been updated successfully!');
    }

    /**
     * add kill from encounterkills table in database
     * 
     * @return void
     */
    private function _addKill() {
        DBObjects::addKill($this->_formFields);

        if ( Functions::validateImage($this->_formFields->screenshot) ) {
            $imagePath = ABS_FOLD_KILLSHOTS . $this->_formFields->guildId . '-' . $this->_formFields->encounter;

            if ( file_exists($imagePath) ) {
                unlink($imagePath);
            }

            move_uploaded_file($this->_formFields->screenshot['tmp_name'], $imagePath);
        }

        $this->_guildDetails = $this->_getUpdatedGuildDetails($this->_guildDetails->_guildId);

        $this->_dialogOptions = array('title' => 'Success', 'message' => 'Your kill has been submitted successfully! Standings and Rankings will be updated accordingly!');
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

    /**
     * adds edit/remove option properties to encounter details object
     * 
     * @return void
     */
    private function mergeOptionsToEncounters() {
        foreach( $this->_guildDetails->_encounterDetails as $encounterId => $encounterDetails ) {
            $newEncounterDetails = new stdClass();

            $encounterProperties = $encounterDetails->getProperties();

            foreach ( $encounterProperties as $key => $value ) {
                $newEncounterDetails->$key = $value;
            }

            $optionsString = '';
            $optionsString .= $this->generateInternalHyperlink(UserPanelModel::SUB_KILLS, UserPanelModelKill::KILLS_EDIT . '/' . $this->_guildDetails->_guildId .'/' . $encounterId, 'Edit', true);
            $optionsString .= ' | ';
            $optionsString .= $this->generateInternalHyperlink(UserPanelModel::SUB_KILLS, UserPanelModelKill::KILLS_REMOVE . '/' . $this->_guildDetails->_guildId . '/' . $encounterId, 'Delete', true);

            $newEncounterDetails->_options = $optionsString;

            $this->_guildDetails->_encounterDetails->$encounterId = $newEncounterDetails;
        }
    }
}