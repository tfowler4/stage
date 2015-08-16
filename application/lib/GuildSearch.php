<?php

class GuildSearch {
    protected static $_queryString;
    public static $searchResults = array();

    public static function getSearchResults() {
        self::$_queryString = Post::get('queryTerm');

        if ( empty(self::$_queryString) ) { return ''; }

        $dbh = DbFactory::getDbh();

        $query = $dbh->prepare(sprintf(
            "SELECT * 
               FROM %s
              WHERE name LIKE '%s' 
           ORDER BY name DESC", 
                    DbFactory::TABLE_GUILDS,
                    '%' . self::$_queryString . '%'
                    ));
        $query->execute();
        
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $guildId = $row['guild_id'];
            self::$searchResults[$guildId] = CommonDataContainer::$guildArray[$guildId];
        }

        return self::$searchResults;
    }
}