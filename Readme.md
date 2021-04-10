# This project is built using the Slim Microframework

Upon cloning, run `composer install` to download and install all dependencies.

If serving with Apache, make sure the mod_rewrite module is enabled.

Edit the configuration variables in the /configs directory. It is recommended
to copy these files to a different directory outside the project root and edit the config/core.json
file to reflect the new location to avoid accidentally commiting them.

You can access the routes as defined in the index.php file and also on the postman workspace for this project.
All endpoints are versioned as /v1/... 

# INSTALL DB

Import the sql scripts using mysql source command

    source '<project_absolute_path>/schema/index.sql'

# Consuming the Endpoints

The following dynamic route has been configured in `index.php` 

    $app->group('/', function(){
	$this->map(
		['GET', 'POST', 'PUT', 'DELETE'],
		'{version}/{module}/{resource}/{action}[/{resourceId}]',

As seen from the above snippet, the endpoints are methods within classes within
the modules located in `/src`.

As an example to access the endpoint for bootstrapping a new user account, the following
route will be posted to.
    `
    /v1/user/user-account/new-account
    `

`newAccount` is a method in the `UserAccount` class of the `User` module (namespace).
