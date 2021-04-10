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
 * class RFHAPI\User\UserAccount\Account.
 *
 * User Account Controller
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 17/01/2021 02:37
 */
class Account {

	/**
     * Creates new user account
     *
     * @param string $email
     * @param string $password
     *
     * @return array
     */
	public static function newAccount(string $email, string $password){
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);

		$result = DBQueryFactory::insert("Users_Account", [
			"UserEmail"=>QB::wrapString($email, "'"),
			"PasswordHash"=>QB::wrapString($passwordHash, "'")
		], false);

		if (!$result['lastInsertId']){
			//throw an exception, insert was unsuccessful
		}	
		
		return $result;
	}

	public static function viewAccounts(){
	}

	/**
     * Check if an email has already been registered
     *
     * @param string $email
     *
     * @return bool
     */
	public static function checkAccountExistsByEmail(string $email){
		$query = "SELECT UserId FROM Users_Account WHERE UserEmail = '$email'";
		$result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

		return count($result) == 1;
	}

	public static function checkAccountExistsById(int $accountId){
		$query = "SELECT UserId FROM Users_Account WHERE UserId = $accountId";
		$result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

		return count($result) == 1;
	}

	public static function getUserId(string $email){
		$query = "SELECT UserId FROM Users_Account WHERE UserEmail = '$email';";
		$result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

		return $result[0]["UserId"] ?? -1;
	}

	public static function updateKycGroup(int $userId){
		$currGroup = \RFHApi\User\UserAccount\UserKyc::determineKycGroup($userId)["GroupId"];
		
		$result = -1;

		if ($currGroup == 0){ //use default KYC Group
			$defaultGroup = UserKyc::getLowestPriorityGroup();
			$currGroup = $defaultGroup["GroupId"] ?? 0;
		}

		if ($currGroup != 0){
			$query = "UPDATE Users_Account SET KycGroupId = $currGroup, LastModified = CURRENT_TIMESTAMP WHERE UserId = $userId";
			$result = DBConnectionFactory::getConnection()->exec($query);
		}

		return ["status"=>$result];
	}
}