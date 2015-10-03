<?php

/**
 * dungeon administration
 */
class AdministratorModelDungeon {
    protected $_action;
    protected $_dbh;

    /**
     * constructor
     */
    public function __construct($action, $dbh) {
        $this->_action = $action;
        $this->_dbh    = $dbh;

        if ( Post::get('dungeon') || Post::get('submit') ) {
            switch ($this->_action) {
                case "add":
                    $this->addNewDungeon();
                    break;
                case "edit":
                    $this->editDungeon(Post::get('dungeon'));
                    break;
                case "remove":
                    $this->removeDungeon();
                    break;
            }
        } else {
            die;
        }
    }

    /**
     * insert new dungeon details into the database
     *
     * @return void
     */
    public function addNewDungeon() {
        $dungeon   = Post::get('create-dungeon-name');
        $tier      = Post::get('create-dungeon-tier-name');
        $numOfMobs = Post::get('create-dungeon-number-of-mobs');

        $tierDetails     = CommonDataContainer::$tierArray[$tier];
        $tierId          = $tierDetails->_tierId;
        $newDungeonCount = $tierDetails->_numOfDungeons + 1;

        $createDungeonQuery = $this->_dbh->prepare(sprintf(
            "INSERT INTO %s
            (name, tier, mobs)
            values('%s', '%s', '%s')",
            DbFactory::TABLE_DUNGEONS,
            $dungeon,
            $tier,
            $numOfMobs
            ));
        $createDungeonQuery->execute();

        $updateTierQuery = $this->_dbh->prepare(sprintf(
            "UPDATE %s
            SET dungeons = '%s'
            WHERE tier_id = '%s'",
            DbFactory::TABLE_TIERS,
            $newDungeonCount,
            $tierId
        ));
        $updateTierQuery->execute();
        die;
    }

    /**
     * create html to prepare form and display all necessary dungeon details
     * 
     * @param  DungeonDetails $dungeonDetails [ dungeon details object ]
     * 
     * @return string                         [ return html containing specified dungeon details ]
     */
    public function editDungeonHtml($dungeonDetails) {
        $raidSize    = array(10, 20);
        $dungeonType = array(0 => 'Standard Dungeon', 1 => 'Special Dungeon (Unranked)');
        $launchDate  = explode(' ', $dungeonDetails->_dateLaunch);

        $html = '';
        $html .= '<form class="admin-form dungeon edit details" id="form-dungeon-edit-details" method="POST" action="' . PAGE_ADMIN . '">';
        $html .= '<table class="admin-dungeon-listing">';
        $html .= '<thead>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $html .= '<tr><td><input hidden type="text" name="edit-dungeon-id" value="' . $dungeonDetails->_dungeonId . '"/></td></tr>';
        $html .= '<tr><th>Dungeon Name</th></tr>';
        $html .= '<tr><td><input class="admin-textbox" type="text" name="edit-dungeon-name" value="' . $dungeonDetails->_name . '"/></td></tr>';
        $html .= '<tr><th>Abbreviation</th></tr>';
        $html .= '<tr><td><input class="admin-textbox" type="text" name="edit-dungeon-abbreviation" value="' . $dungeonDetails->_abbreviation . '"/></td></tr>';
        $html .= '<tr><th>Tier</th></tr>';
        $html .= '<tr><td><select class="admin-select tier" name="edit-dungeon-tier-number">';
        $html .= '<option value="">Select Tier</option>';
            foreach( CommonDataContainer::$tierArray as $tier => $tierDetails ):
                if ( $tier == $dungeonDetails->_tier ):
                    $html .= '<option value="' . $tierDetails->_tier . '" selected>' . $tierDetails->_tier . ' - ' . $tierDetails->_name . '</option>';
                else:
                    $html .= '<option value="' . $tierDetails->_tier . '">' . $tierDetails->_tier . ' - ' . $tierDetails->_name . '</option>';
                endif;
            endforeach;
        $html .= '</select></td></tr>';
        $html .= '<tr><th>Raid Size</th></tr>';
        $html .= '<tr><td><select class="admin-select players" name="edit-dungeon-players">';
            foreach ($raidSize as $players):
                if ( $players == $dungeonDetails->_raidSize ):
                    $html .= '<option value="' . $players . '" selected>' . $players . '-Man</option>';
                else:
                    $html .= '<option value="' . $players . '">' . $players . '-Man</option>';
                endif;
            endforeach;
        $html .= '</select></td></tr>';
        $html .= '<tr><th>Dungeon Type</th></tr>';
        $html .= '<tr><td><select class="admin-select dungeon" name="edit-dungeon-type">';
            foreach ($dungeonType as $type => $typeValue):
                if ( $type == $dungeonDetails->_type ):
                    $html .= '<option value="' . $type . '" selected>' . $type . ' - ' . $typeValue . '</option>';
                else:
                    $html .= '<option value="' . $type . '">' . $type . ' - ' . $typeValue . '</option>';
                endif;
            endforeach;
        $html .= '</select></td></tr>';
        $html .= '<tr><th>EU Time Difference</th></tr>';
        $html .= '<tr><td><input class="admin-textbox" type="text" name="edit-dungeon-eu-diff" value="' . $dungeonDetails->_euTimeDiff . '"/></td></tr>';
        $html .= '<tr><th>Launch Date</th></tr>';
        $html .= '<tr><td><select class="admin-select month" name="edit-dungeon-launch-month">';
            foreach( CommonDataContainer::$monthsArray as $month => $monthValue):
                if ( $month == date('m', strtotime($dungeonDetails->_dateLaunch)) ):
                    $html .= '<option value="' . $month . '" selected>' . $monthValue . '</option>';
                else:
                    $html .='<option value="' . $month . '">' . $monthValue . '</option>';
                endif;
            endforeach;
        $html .= '</select>';
        $html .= '<select class="admin-select day" name="edit-dungeon-launch-day">';
            foreach( CommonDataContainer::$daysArray as $day => $dayValue):
                if ( $day == $launchDate[1] ):
                    $html .= '<option value="' . $day . '" selected>' . $dayValue . '</option>';
                else:
                    $html .='<option value="' . $day . '">' . $dayValue . '</option>';
                endif;
            endforeach;
        $html .= '</select>';
        $html .= '<select class="admin-select year" name="edit-dungeon-launch-year">';
            foreach( CommonDataContainer::$yearsArray as $year => $yearValue):
                if ( $year == $launchDate[2] ):
                    $html .= '<option value="' . $year . '" selected>' . $yearValue . '</option>';
                else:
                    $html .= '<option value="' . $year . '">' . $yearValue . '</option>';
                endif;
            endforeach;
        $html .= '</select></td></tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '<div class="vertical-separator"></div>';
        $html .= '<input id="admin-submit-tier-edit-action" type="hidden" name="submit" value="submit" />';
        $html .= '<input id="admin-submit-dungeon-edit" type="submit" value="Submit" />';
        $html .= '</form>';

        return $html;
    }

    /**
     * get id from drop down selection to obtain the specific dungeon details
     * and pass that array to editDungeonHtml to display
     * 
     * @param  string $dungeonId [ id of a specific dungeon ]
     * 
     * @return void
     */
    public function editDungeon($dungeonId) {
        // if the submit field is present, update dungeon data
        if ( Post::get('submit') ) {
            $dungeonId    = Post::get('edit-dungeon-id');
            $dungeon      = Post::get('edit-dungeon-name');
            $abbreviation = Post::get('edit-dungeon-abbreviation');
            $tier         = Post::get('edit-dungeon-tier-number');
            $raidSize     = Post::get('edit-dungeon-players');
            $launchDate   = Post::get('edit-dungeon-launch-year') . '-' . Post::get('edit-dungeon-launch-month') . '-' . Post::get('edit-dungeon-launch-day');
            $dungeonType  = Post::get('edit-dungeon-type');
            $euTimeDiff   = Post::get('edit-dungeon-eu-diff');

            $query = $this->_dbh->prepare(sprintf(
                "UPDATE %s
                SET name = '%s', 
                    abbreviation = '%s', 
                    tier = '%s', 
                    players = '%s', 
                    date_launch = '%s', 
                    dungeon_type = '%s', 
                    eu_diff = '%s'
                WHERE dungeon_id = '%s'",
                DbFactory::TABLE_DUNGEONS,
                $dungeon,
                $abbreviation,
                $tier,
                $raidSize,
                $launchDate,
                $dungeonType,
                $euTimeDiff,
                $dungeonId
                ));
            $query->execute();
        } else {
            $html           = '';
            $dungeonDetails = CommonDataContainer::$dungeonArray[$dungeonId];

            $html = $this->editDungeonHtml($dungeonDetails);

            echo $html;
        }

        die;
    }

    /**
     * delete from dungeon_table by specified id
     * 
     * @return void
     */
    public function removeDungeon() {
        $dungeonId = Post::get('remove-dungeon-id');

        $dungeonDetails  = CommonDataContainer::$dungeonArray[$dungeonId];
        $tierDetails     = CommonDataContainer::$tierArray[$dungeonDetails->_tier];
        $tierId          = $tierDetails->_tierId;
        $newDungeonCount = $tierDetails->_numOfDungeons - 1;

        $deleteDungeonQuery = $this->_dbh->prepare(sprintf(
            "DELETE 
               FROM %s
              WHERE dungeon_id = '%s'",
            DbFactory::TABLE_DUNGEONS,
            $dungeonId
            ));
        $deleteDungeonQuery->execute();

        $updateTierQuery = $this->_dbh->prepare(sprintf(
            "UPDATE %s
            SET dungeons = '%s'
            WHERE tier_id = '%s'",
            DbFactory::TABLE_TIERS,
            $newDungeonCount,
            $tierId
        ));
        $updateTierQuery->execute();
        die;
    }
}