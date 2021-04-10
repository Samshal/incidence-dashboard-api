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

namespace RFHAPI\User\UserAccount;

use EmmetBlue\Core\Factory\DatabaseConnectionFactory as DBConnectionFactory;
use EmmetBlue\Core\Factory\DatabaseQueryFactory as DBQueryFactory;
use EmmetBlue\Core\Builder\QueryBuilder\QueryBuilder as QB;

/**
 * class RFHAPI\User\UserAccount\AccountType.
 *
 * User Account Type Controller
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 17/01/2021 02:52
 */
class AccountType {
	
	/**
     * Assigns a new user account to an account type.
     *
     * @param int $userId
     * @param int $accountType
     *
     * @return array
     */
	public static function addAccountType(int $userId, int $accountType){
          $query = "INSERT INTO Users_UserAccountType (UserId, AccountType) VALUES ($userId, $accountType)";
		$result = DBConnectionFactory::getConnection()->prepare($query)->execute();  

		return $result;
	}

	/**
     * Retrieve all user account types
     *
     * @param int $userId
     *
     * @return array
     */
	public static function viewAccountTypes(int $userId){
		$query = "SELECT a.*, b.TypeName, b.TypeDescription FROM Users_UserAccountType a INNER JOIN Users_AccountTypes b ON a.AccountType = b.TypeId WHERE a.UserId = $userId";
		$result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

		return $result;
	}
}