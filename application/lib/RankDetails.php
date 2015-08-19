<?php
class RankDetails extends DetailObject {
    protected $_id;
    protected $_system;
    protected $_points;
    protected $_rank;
    protected $_prevRank;
    protected $_trend;

    public function __construct($params, $id) {
        $this->_system = $params[0];
        $this->_id     = $id;
        $this->_points = !empty($params[1]) ? $params[1] : 0;

        if ( !empty($params[2]) ) { $this->_rank = new RankView(array_slice($params, 2, 4)); }
        if ( !empty($params[6]) ) { $this->_trend = new RankView(array_slice($params, 6, 4)); }
        if ( !empty($params[10]) ) { $this->_prevRank = new RankView(array_slice($params, 10, 4)); }
    }
}