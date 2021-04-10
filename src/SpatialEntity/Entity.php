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

namespace RFHApi\SpatialEntity;

/**
 * class RFHApi\SpatialEntity\Entity
 *
 * Entity Visitor
 * 
 * This class CRUD's Geographic/Geospatial Features.
 * A geographic feature is anything in the world that has a location. An Entity object can be:
 * An entity. For example, a farm.
 * A space. For example, a city, country, etc.
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 27/01/2021 08:50
 */
class Entity {

	/**
     * Creates new Entity 
     *
     * @param array $data
     *
     * @return array
     */
	public function newEntity(array $data){
		$return_result = Entity\Entity::newEntity($data);

		return $return_result;
	}
	
	public function viewEntitiesByType(array $data){
		return Entity\Entity::viewEntitiesByType($data);
	}
	
	public function viewEntityChildren(array $data){
		return Entity\Entity::viewEntityChildren($data);
	}
	
	public function viewEntityTypes(){
		return Entity\Entity::viewEntityTypes();
	}
    
    public function newMetadata(array $data){
        
    }
}