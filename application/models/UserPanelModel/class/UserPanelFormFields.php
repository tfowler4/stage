<?php

/**
 * guild & raid team add/edit form
 */
class GuildFormFields {
    public $guildId;
    public $guildName;
    public $faction;
    public $server;
    public $region;
    public $country;
    public $guildLeader;
    public $website;
    public $facebook;
    public $twitter;
    public $guildLogo;
    public $active;
}

/**
 * user edit form
 */
class UserFormFields {
    public $userId;
    public $email;
    public $oldPassword;
    public $newPassword;
    public $retypeNewPassword;
}

/**
 * kill submission form
 */
class KillSubmissionFormFields {
    public $guildId;
    public $encounter;
    public $dateMonth;
    public $dateDay;
    public $dateYear;
    public $dateHour;
    public $dateMinute;
    public $screenshot;
    public $video;
    public $videoId;
    public $videoTitle;
    public $videoUrl;
    public $videoType;
}