<?php
// RIFT
define('SITE_TITLE',           'RIFT Progress');
define('SITE_TITLE_SHORT',     'RIFT Progress');
define('GAME_NAME_2',          'Storm Legion');
define('GAME_NAME_3',          'Nightmare Tide');
define('GAME_URL',             'http://www.riftgame.com');
define('META_AUTHOR',          "Terry 'Raive' Fowler");
define('META_KEYWORDS',        "RIFT, RIFT raiding, RIFT progress, progress, raiding, progression, tracker, tracking, rank, ranking, top 25, guild");
define('META_DESCRIPTION',     "RIFT's #1 Resource for raid progression tracking.");
define('LATEST_TIER',          8);
define('EU_TIME_DIFF',         28800); // 8 *3600 = 28800
define('NA_PATCH_TIME',        'N/A (Inconsistant)'); //"UTC -8 Hours (PST)"
define('EU_PATCH_TIME',        'N/A (Inconsistant)'); //"UTC +0 Hours (GMT)"
define('FREEZE_KILL_COUNT',    0);
define('FREEZE_KILL_DATE',     0);
define('BASE_POINT_VALUE',     1000);
define('UPDATE_FREQ',          30);
define('POINT_SYSTEM_DEFAULT', 'QP');
define('RELEASE_YEAR',         2011);
define('REQUIRE_SCREENSHOT',   1);
define('REQUIRE_ENCOUNTERS',   1);
define('EMAIL_ADMIN',          'administrator@topofrift.com');
define('COMPANY_1',            'Trion Worlds');
define('LINK_COMPANY_1',       'http://www.trionworlds.com');
define('LINK_GAME_1',          'http://www.riftgame.com');
define('LINK_FACEBOOK',        'http://www.facebook.com/RiftProgress');
define('LINK_TWITTER',         'http://twitter.com/RiftProgress');
define('LINK_GOGGLE',          'http://plus.google.com/+Rift-progress/');
define('COPYRIGHT',            '&copy; ' . RELEASE_YEAR . ' ' . SITE_TITLE . ' - All Rights Reserved.');

define('FACTIONS',   serialize(array('Defiant', 'Guardian')));
define('RAID_SIZES', serialize(array('20', '10')));

define('GOOGLE_ANALYTICS', "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

                            ga('create', 'UA-39972522-1', 'topofrift.com');
                            ga('send', 'pageview');");

// Go to Graph API Explorer
// Select Application (Rift Progress)
// Click Get Access Token
// Place Access Token in this url
// https://graph.facebook.com/oauth/access_token?client_id=816307315051965&client_secret=97e6d3e3e7f6decc497428ec53b08cdd&grant_type=fb_exchange_token&fb_exchange_token=***ACCESS TOKEN HERE***

// Facebook
//define('FACEBOOK_APP_ID',     '816307315051965');
//define('FACEBOOK_PAGE_ID',    '1404349913116107');
//define('FACEBOOK_SECRET',     '97e6d3e3e7f6decc497428ec53b08cdd');
//define('FACEBOOK_TOKEN_USER', 'CAALmbVuKjb0BAHJiOeYyQ5ZCJ2YeMBg0XXXTdQjSdubnP2XKGtZC93JOozcEl6HEScWJDDOCIyxhzSQklwZAQW6KtY3ywOWS1EQRZCBaTooUKz2VyPbIXB7WSYbsvzBS2aMGUNZBJ5Cn8foVbSw76ZA8IlRAdbUFMS8EUjYsgEHgKzENgYYXcB');
//define('FACEBOOK_TOKEN_APP',  '816307315051965|U3PwC5ZAXEE84-FSKONeUQx8o4Q');

// Twitter
define('TWITTER_KEY',          'LbFIFyK8qx0GY72rlu52Q');
define('TWITTER_SECRET',       'VMgEPXl9BEusyJDkCuMJvvug0SyxfqieiKcxcKKHFg');
define('TWITTER_TOKEN',        '1697399862-fB2jnBQqAjgK0vf8hfiCldGchzNWznno6gDq6ya');
define('TWITTER_TOKEN_SECRET', 'W1gA0kWjrpgM3hals2PjfcuqcqSguX2K8KRDUVP75qUbm');

// Register Questions
define('REGISTER_QUESTIONS', serialize(array('What are the two factions? (Defiant & ?????)', 'Which company developed RIFT? (***** World)')));
define('REGISTER_ANSWERS',   serialize(array('guardian', 'trion')));

// DB Settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'vgtrin5_rift_live');
define('DB_USER', 'vgtrin5_rift');
define('DB_PASS', 'RiftTrinity74108520!');