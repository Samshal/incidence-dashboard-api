<?php
require_once 'vendor/autoload.php';

use IncidenceDashboardApi\Middleware\PermissionGateway as PermissionGatewayMiddleware;

$settings = [
	'settings'=>[
		'determineRouteBeforeAppMiddleware' => true,
		'displayErrorDetails' => true
	]
];

header('Access-Control-Allow-Headers: Content-Type, Cache-Control, X-Requested-With, Authorization');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Origin: *');

$permissionGatewayMiddleware = new PermissionGatewayMiddleware();
$app = new \Slim\App($settings);

$app->post('/{version}/login', function($request, $response, $args){
	$data = $request->getParsedBody();

	$pluginResponse = IncidenceDashboardApi\User\UserSession::login($data);

	if (isset($pluginResponse["status"]) && $pluginResponse["status"]){

		return $response->withJson(["errorStatus"=>false, "errorMessage"=>null, "contentData"=>$pluginResponse], 200);
	}

	return $response->withJson(["errorStatus"=>false, "errorMessage"=>null, "contentData"=>["status"=>false, "reason"=>"Invalid Username or Password"]], 200);
});

$app->group('/', function(){
	$this->map(
		['GET', 'POST', 'PUT', 'DELETE'],
		'{version}/{module}/{resource}/{action}[/{resourceId}]',
		function($request, $response, $args){
			$globalResponseFormat = [
				"body"=>[
					"errorStatus"=>false,
					"errorMessage"=>NULL,
					"contentData"=>[]
				],
				"status"=>200
			];

			if ($request->isGet() || $request->isDelete())
			{
				$parsedBody = $request->getQueryParams();
			}
			else
			{
				$parsedBody = $request->getParsedBody();
			}
			
			if (!is_array($parsedBody)){
				$globalResponse = [
					"body"=>[
						"errorStatus"=>true,
						"errorType"=>"Error",
						"errorMessage"=>"No data detected."
					],
					"status"=>400
				];

				return $response->withJson($globalResponse["body"], $globalResponse["status"]);
			}

			$options = array_merge($args, $parsedBody);
			
			$globalResponse = array_replace_recursive(
				$globalResponseFormat, 
				EmmetBlueMiddleware\Middleware::processor($options, "IncidenceDashboardApi")
			);

			return $response->withJson($globalResponse["body"], $globalResponse["status"]);
	}); //->add(EmmetBlueMiddleware\Middleware::validateRequest());
}); //->add($permissionGatewayMiddleware->getStandardResponse());

$app->run();