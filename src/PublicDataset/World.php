<?php declare (strict_types=1);
/**
 * Visitor Class.
 *
 * This file is part of RFHubAPI, please read the documentation
 * available in the root level of this project
 *
 * @license MIT
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 *
 */

namespace RFHApi\PublicDataset;

/**
 * class RFHApi\PublicDataset\World
 *
 * World Visitor
 * 
 * This class returns list of countries, states, cities in the world
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 30/01/2021 10:09
 */
class World {
    
	public function getCountries(){
		return Controllers\World::getCountries();
    }
    
	public function getStates(int $countryId){
		return Controllers\World::getStates($countryId);
    }
    
	public function getCities(int $stateId){
		return Controllers\World::getCities($stateId);
	}
}