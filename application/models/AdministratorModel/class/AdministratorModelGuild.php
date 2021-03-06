<?php

/**
 * guild administration
 */
class AdministratorModelGuild {
    protected $_action;
    protected $_dbh;
    protected $_formFields;

    /**
     * constructor
     */
    public function __construct($action, $dbh) {
        $this->_action = $action;
        $this->_dbh    = $dbh;

        if ( Post::get('adminpanel-guild') || Post::get('submit') ) {
            $this->populateFormFields();

            switch ($this->_action) {
                case "add":
                    $this->addNewGuild();
                    break;
                case "edit":
                    $this->editGuild(Post::get('adminpanel-guild'));
                    break;
                case "remove":
                    $this->removeGuild();
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
        $this->_formFields = new AdminGuildFormFields();

        $this->_formFields->guildId     = Post::get('adminpanel-guild-id');
        $this->_formFields->guild       = Post::get('adminpanel-guild');
        $this->_formFields->faction     = Post::get('adminpanel-faction');
        $this->_formFields->server      = Post::get('adminpanel-server');
        $this->_formFields->country     = Post::get('adminpanel-country');
        $this->_formFields->guildLeader = Post::get('adminpanel-guild-leader');
        $this->_formFields->website     = Post::get('adminpanel-website');
        $this->_formFields->facebook    = Post::get('adminpanel-facebook');
        $this->_formFields->twitter     = Post::get('adminpanel-twitter');
        $this->_formFields->guildLogo   = Post::get('adminpanel-screenshot');
        $this->_formFields->active      = Post::get('adminpanel-active');
    }

    /**
     * insert new guild details into the database
     *
     * @return void
     */
    public function addNewGuild() {
        $this->_formFields->guildName = $this->_formFields->guild;
        $this->_formFields->region    = CommonDataContainer::$serverArray[$this->_formFields->server]->_region;

        DbObjects::addGuild($this->_formFields);
        $this->_assignGuildLogo(DbObjects::$insertId);
    }

    /**
     * create html to prepare form and display all necessary guild details
     * 
     * @param  GuildDetails $guildDetails [ guild details object ]
     * 
     * @return string [ return html containing specified dungeon details ]
     */
    public function editGuildHtml($guildDetails) {
        $html = '';

        $html .= '<div class="form-group">';
        $html .= '<label for="" class="control-label col-lg-3 col-md-2">Name</label>';
        $html .= '<div class="col-lg-8 col-md-10">';
        $html .= '<input hidden type="text" name="adminpanel-dungeon-id" value="' . $guildDetails->_guildId . '"/>';
        $html .= '<input type="text" name="adminpanel-dungeon" class="form-control" placeholder="Enter Guild Name" value="' . $guildDetails->_name . '" readonly>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '<label for="" class="control-label col-lg-3 col-md-2">Guild Leader</label>';
        $html .= '<div class="col-lg-8 col-md-10">';
        $html .= '<input type="text" name="adminpanel-dungeon" class="form-control" placeholder="Enter Guild Leader Name" value="' . $guildDetails->_leader . '">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '<label for="" class="control-label col-lg-3 col-md-2">Website</label>';
        $html .= '<div class="col-lg-8 col-md-10">';
        $html .= '<input type="text" name="adminpanel-dungeon" class="form-control" placeholder="Enter Guild Website" value="' . $guildDetails->_website . '">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '<label for="" class="control-label col-lg-3 col-md-2">Faction</label>';
        $html .= '<div class="col-lg-8 col-md-10">';
        $html .= '<select name="adminpanel-dungeon-tier" class="form-control">';
        foreach( CommonDataContainer::$factionArray as $factionId => $factionDetails ) {
            if ( $factionDetails->_name == $guildDetails->_faction ) {
                $html .= '<option value="' . $factionId . '" selected>' .  $factionDetails->_name . '</option>';
            } else {
                $html .= '<option value="' . $factionId . '">' .$factionDetails->_name . '</option>';
            }
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '<label for="" class="control-label col-lg-3 col-md-2">Server</label>';
        $html .= '<div class="col-lg-8 col-md-10">';
        $html .= '<select name="adminpanel-dungeon-tier" class="form-control">';
        foreach( CommonDataContainer::$serverArray as $serverId => $serverDetails ) {
            if ( $serverDetails->_name == $guildDetails->_server ) {
                $html .= '<option value="' . $serverId . '" selected>' . $serverDetails->_region . ' - ' . $serverDetails->_name . '</option>';
            } else {
                $html .= '<option value="' . $serverId . '">' . $serverDetails->_region . ' - ' . $serverDetails->_name . '</option>';
            }
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '<label for="" class="control-label col-lg-3 col-md-2">Country</label>';
        $html .= '<div class="col-lg-8 col-md-10">';
        $html .= '<select name="adminpanel-dungeon-tier" class="form-control">';
        foreach( CommonDataContainer::$countryArray as $countryId => $countryDetails ) {
            if ( $countryDetails->_name == $guildDetails->_country ) {
                $html .= '<option value="' . $countryId . '" selected>' .  $countryDetails->_name . '</option>';
            } else {
                $html .= '<option value="' . $countryId . '">' .$countryDetails->_name . '</option>';
            }
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '<label for="" class="control-label col-lg-3 col-md-2">Facebook</label>';
        $html .= '<div class="col-lg-8 col-md-10">';
        $html .= '<input type="text" name="adminpanel-dungeon" class="form-control" placeholder="Enter Facebook Handle" value="' . $guildDetails->_facebook . '">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '<label for="" class="control-label col-lg-3 col-md-2">Twitter</label>';
        $html .= '<div class="col-lg-8 col-md-10">';
        $html .= '<input type="text" name="adminpanel-dungeon" class="form-control" placeholder="Enter Twitter Handle" value="' . $guildDetails->_twitter . '">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '<label for="" class="control-label col-lg-3 col-md-2">Guild Logo</label>';
        $html .= '<div class="col-lg-8 col-md-10">';
        $html .= '<input type="file" name="adminpanel-guild-logo" class="form-control">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '<label for="" class="control-label col-lg-3 col-md-2">Active</label>';
        $html .= '<div class="col-lg-8 col-md-10">';
        $html .= '<select name="adminpanel-dungeon-tier" class="form-control">';
        foreach( unserialize(GUILD_STATUS) as $statusId => $status ) {
            if ( $statusId == $guildDetails->_active ) {
                $html .= '<option value="' . $statusId . '" selected>' .  $statusId . ' - ' . $status . '</option>';
            } else {
                $html .= '<option value="' . $statusId . '">' .$statusId . ' - ' . $status . '</option>';
            }
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';

        /*
        $html .= '<form class="admin-form guild edit details" id="form-guild-edit-details" method="POST" action="' . PAGE_ADMIN . '" >';
        $html .= '<table class="admin-guild-listing">';
        $html .= '<thead>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $html .= '<tr><th>Date Created</th></tr>';
        $html .= '<tr><td>' . $guildDetails->_dateCreated . '</td></tr>';
        $html .= '<tr><th>Guild Name</th></tr>';
        $html .= '<tr><td><input hidden type="text" name="adminpanel-guild-id" value="' . $guildDetails->_guildId . '"/><input class="admin-textbox" type="text" name="adminpanel-guild" value="' . $guildDetails->_name . '"/></td></tr>';
        $html .= '<tr><th>Leader</th></tr>';
        $html .= '<tr><td><input class="admin-textbox" type="text" name="adminpanel-guild-leader" value="' . $guildDetails->_leader . '"/></td></tr>';
        $html .= '<tr><th>Website</th></tr>';
        $html .= '<tr><td><input class="admin-textbox" type="text" name="adminpanel-website" value="' . $guildDetails->_website . '"/></td></tr>';
        $html .= '<tr><th>Facebook</th></tr>';
        $html .= '<tr><td><input class="admin-textbox" type="text" name="adminpanel-facebook" value="' . $guildDetails->_facebook . '"/></td></tr>';
        $html .= '<tr><th>Twitter</th></tr>';
        $html .= '<tr><td><input class="admin-textbox" type="text" name="adminpanel-twitter" value="' . $guildDetails->_twitter . '"/></td></tr>';
        $html .= '<tr><th>Faction</th></tr>';
        $html .= '<tr><td><select class="admin-select faction" name="adminpanel-faction">';
            foreach( CommonDataContainer::$factionArray as $factionId => $factionDetails ) {
                if ( $factionDetails->_name == $guildDetails->_faction ) {
                    $html .= '<option value="' . $factionId . '" selected>' .  $factionDetails->_name . '</option>';
                } else {
                    $html .= '<option value="' . $factionId . '">' .$factionDetails->_name . '</option>';
                }
            }
        $html .= '</select></td></tr>';
        $html .= '<tr><th>Server</th></tr>';
        $html .= '<tr><td><select class="admin-select server" name="adminpanel-server">';
            foreach( CommonDataContainer::$serverArray as $serverId => $serverDetails ) {
                if ( $serverDetails->_name == $guildDetails->_server ) {
                    $html .= '<option value="' . $serverId . '" selected>' . $serverDetails->_region . ' - ' . $serverDetails->_name . '</option>';
                } else {
                    $html .= '<option value="' . $serverId . '">' . $serverDetails->_region . ' - ' . $serverDetails->_name . '</option>';
                }
            }
        $html .= '</select></td></tr>';
        $html .= '<tr><th>Country</th></tr>';
        $html .= '<tr><td><select class="admin-select faction" name="adminpanel-country">';
            foreach( CommonDataContainer::$countryArray as $countryId => $countryDetails ) {
                if ( $countryDetails->_name == $guildDetails->_country ) {
                    $html .= '<option value="' . $countryId . '" selected>' .  $countryDetails->_name . '</option>';
                } else {
                    $html .= '<option value="' . $countryId . '">' .$countryDetails->_name . '</option>';
                }
            }
        $html .= '</select></td></tr>';
        $html .= '<tr><th>Active</th></tr>';
        $html .= '<tr><td><input class="admin-textbox" type="text" name="adminpanel-active" value="' . $guildDetails->_active . '"/></td></tr>';
        $html .= '<tr><th>Guild Logo</th></tr>';
        $html .= '<tr><td><input type="file" name="adminpanel-screenshot"></td></tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '<div class="vertical-separator"></div>';
        $html .= '<input id="admin-submit-tier-edit-action" type="hidden" name="submit" value="submit" />';
        $html .= '<input id="admin-submit-guild-edit" type="submit" value="Submit" />';
        $html .= '</form>';
        */
        return $html;
    }

    /**
     * get id from drop down selection to obtain the specific guild details
     * and pass that array to editGuildHtml to display
     * 
     * @param  string $guildId [ id of a specific guild ]
     * 
     * @return void
     */
    public function editGuild($guildId) {
        // if the submit field is present, update guild data
        if ( Post::get('submit') ) {
            $guildDetails = CommonDataContainer::$guildArray[$this->_formFields->guildId];
            $this->_formFields->guildName = $this->_formFields->guild;
            $this->_formFields->region    = CommonDataContainer::$serverArray[$this->_formFields->server]->_region;

            DbObjects::editGuild($this->_formFields, $guildDetails);
            if ( !empty($this->_formFields->guildLogo['tmp_name']) ) { $this->_assignGuildLogo($guildDetails->_guildId); }
        } else {
            $html         = '';
            $guildDetails = CommonDataContainer::$guildArray[$guildId];

            $html = $this->editGuildHtml($guildDetails);
            
            echo $html;
        }
    }

    /**
     * delete from guild_table by specified id
     * 
     * @return void
     */
    public function removeGuild() {
        $guildDetails               = CommonDataContainer::$guildArray[$this->_formFields->guild];
        $this->_formFields->guildId = $this->_formFields->guild;

        DbObjects::removeGuild($this->_formFields);
        
        // Guild is a child of a parent guild, update parent's info
        if ( !empty($this->_guildDetails->_parent) && $this->_guildDetails->_parent != '0' ) {
            $parentId    = $this->_guildDetails->_parent;
            $parentGuild = CommonDataContainer::$guildArray[$parentId];

            $parentGuildChildren = $parentGuild->_child;

            $childrenIdArray = explode('||', $parentGuildChildren);

            foreach( $childrenIdArray as $index => $guildId ) {
                if ( $childrenIdArray[$index] == $this->_guildDetails->_guildId ) { unset($childrenIdArray[$index]); } 
            }

            $sqlChild = '';

            if ( !empty($childrenIdArray) ) {
                $sqlChild = implode("||", $childrenIdArray);
            }

            DbObjects::removeChildGuild($sqlChild, $parentId);
        } else if ( !empty($this->_guildDetails->_child) ) {
            $childrenIdArray = explode('||', $this->_guildDetails->_child);

            foreach( $childrenIdArray as $index => $guildId ) {
                $childForm           = new stdClass();
                $childForm->$guildId = $guildId;
                $childForm           = $childForm;

                DbObjects::removeGuild($childForm);
                $this->_removeGuildLogo($childForm->guildId);
            }
        }
        
        $this->_removeGuildLogo($this->_formFields->guildId);
    }

    /**
     * assign guild logo image to guild if one is supplied, else assign default
     * 
     * @param  integer $guildId [ id of guild ]
     * 
     * @return void
     */
    private function _assignGuildLogo($guildId) {
        $imagePath        = ABS_FOLD_SITE_GUILD_LOGOS . 'logo-' . $guildId;
        $defaultImagePath = ABS_FOLD_SITE_LOGOS . 'guild_default_logo.png';

        if ( Functions::validateImage($this->_formFields->guildLogo) ) {
            move_uploaded_file($this->_formFields->guildLogo['tmp_name'], $imagePath);
        } else {
            copy($defaultImagePath, $imagePath);
        }
    }

    /**
     * remove guild logo image from filesystem
     * 
     * @return void
     */
    private function _removeGuildLogo($guildId) {
        $imagePath = ABS_FOLD_SITE_GUILD_LOGOS . 'logo-' . $guildId;

        if ( file_exists($imagePath) ) {
            unlink($imagePath);
        }
    }
}