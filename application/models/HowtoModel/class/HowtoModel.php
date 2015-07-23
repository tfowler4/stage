<?php
class HowtoModel extends Model {
    const PAGE_TITLE = 'How-To use ' . SITE_TITLE;

    const PANE_TIDBITS = array(
            'Update Frequency'    => 'Every 10 Minutes',
            'NA Patch Time'       => 'N/A (Inconsistent)',
            'EU Patch Time'       => 'N/A (Inconsistent)',
            'Freeze Kill Counter' => 'N/A',
            'Freeze Kill Date'    => 'N/A',
            'Base Point Value'    => '1,000 Points'
        );

    const GLOSSARY = array(
            'NbG'  => 'Number of Guilds Evaluated',
            'NbGD' => 'Number of Guilds Downed Encounter',
            'NbT'  => 'Time after World First Encounter Clear',
            'NbHK' => 'Number of Most Killed Encounter',
            'NKE'  => 'Number of Kills for Specific Encounter',
            'BEPV' => 'Base Encounter Point Value'
        );

    public function __construct($module, $params) {
        parent::__construct($module);

        $this->title = self::PAGE_TITLE;
    }
}