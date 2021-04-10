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
 * class RFHApi\PublicDataset\Controllers\Crops
 *
 * Crops Controller
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 30/01/2021 10:13
 */
class Crops {
    public function getCrops(){
        $cropsJson = file_get_contents(dirname(__FILE__)."\bin\crops.json");
        $crops = json_decode($cropsJson);

        return $crops;
    }
}