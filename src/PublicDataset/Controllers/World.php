<?php declare (strict_types=1);
/**
 * Controller Class.
 *
 * This file is part of RFHubAPI, please read the documentation
 * available in the root level of this project
 *
 * @license MIT
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 *
 */


namespace RFHApi\PublicDataset\Controllers;

use EmmetBlue\Core\Connection\ConnectionAdapter;


/**
 * class RFHApi\PublicDataset\Controllers\World
 *
 * World Controller
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 30/01/2021 10:13
 */
class World {
    private function getWorldDBConnection(){
        $dbPath = dirname(__FILE__)."\bin\world.sqlite";

        $dbAdapter = new ConnectionAdapter("SQLite", [$dbPath], "", "");

        return $dbAdapter->getConnection();
    }

	public function getCountries(){
        $query = "SELECT * FROM countries;";
        $result = self::getWorldDBConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }
    
	public function getStates(int $countryId){
        $query = "SELECT * FROM states where country_id=$countryId;";
        $result = self::getWorldDBConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
        
    }
    
	public function getCities(int $stateId){
        $query = "SELECT * FROM cities where state_id=$stateId;";
        $result = self::getWorldDBConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
	}
}