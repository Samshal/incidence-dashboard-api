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
 * class RFHApi\PublicDataset\Crops
 *
 * Crops Visitor
 * 
 * This class returns list of countries, states, cities in the Crops
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 30/01/2021 10:09
 */
class Crops {
    public function getCrops(){
        return Controllers\Crops::getCrops();
    }
}