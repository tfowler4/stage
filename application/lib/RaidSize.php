<?php

/**
 * raid size data object
 */
class RaidSize extends DataObject {
    protected $_raidSize;
    protected $_numOfDungeons;
    protected $_numOfEncounters;
    protected $_dungeons;
    protected $_encounters;
    protected $_abbreviation;

    /**
     * constructor
     */
    public function __construct($raidSize) {
        $this->_raidSize        = $raidSize;
        $this->_numOfDungeons   = 0;
        $this->_numOfEncounters = 0;
        $this->_dungeons        = $this->_getDungeons($raidSize);
        $this->_encounters      = $this->_getEncounters($raidSize);
        $this->_abbreviation    = $raidSize . 'M';
    }

    /**
     * get all dungeons with a specific raid size
     *
     * @param  string $raidSize [ number of players in raid ]
     * 
     * @return object [ property containing all encounters from a raid size ]
     */
    private function _getDungeons($raidSize) {
        $property = new stdClass();

        foreach( CommonDataContainer::$dungeonArray as $dungeonId => $dungeon_details ) {
            if ( $dungeon_details->_raidSize == $raidSize ) { $property->$dungeonId = $dungeon_details; $this->_numOfDungeons++; }
        }

        return $property;
    }

    /**
     * get all encounters for a specific raid size
     *
     * @param  string $raidSize [ number of players in raid ]
     * 
     * @return object [ property containing all encounters from a raid size ]
     */
    private function _getEncounters($raidSize) {
        $property = new stdClass();

        foreach( CommonDataContainer::$encounterArray as $encounterId => $encounter_details ) {
            if ( $encounter_details->_raidSize == $raidSize ) { $property->$encounterId = $encounter_details; $this->_numOfEncounters++; }
        }

        return $property;
    }
}