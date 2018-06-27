# Phalcon REST API (JWT) example

This project utilizes Phalcon PHP framework, MongoDB and JWT firebase library to implement REST API service.
It demonstrates how to implement REST API features and can be used as example REST API project or a starting point.

application/index.php is an entry point of the application and it contains several api examples which 
demonstrate how to authenticate user using JWT (Json Web Token), how to check is uses is authenticated (using JWT token) 
when only-for-authenticated-users parts of the api are being accessed and it also demonstrates how to use services 
which use MongoDB to store data.

Finally application/tests folder demonstrates how you can test your API using PHPUnit and Guzelle library which is used
to perform various HTTP requests.

## Installation

This is a project based on official Phalcon Docker Edition. It contains PHP 7, Apache 2 and MongoDB. So you will need
to install docker to be able to use it.

This is how to set this project up on your computer:

- clone this repository into directory you prefer (for example D:\dev\jwtrest or any other)
- install docker (and docker-compose)
- Open your hosts file (/etc/hosts) and enter a new line like this:
127.0.0.1 phalcon.api
* if you want something other than phalcon.api then search/replace all the "phalcon.api" entries in this repository
  with the name you like (for example phalcon.local)
- now go to project directory and run "docker-compose build". It will download all images required.
- run "docker-compose up -d" to run containers
    * after that http://phalcon.api will be up and running

- current mongo db hack required to allow mongodb connections:
    - make sure containers are running ("docker-compose up -d")
    - run the following command, where "039" is part of id of running mongo container:
        - docker inspect 039  | grep "IPAddress"
        * to find id of mongo container run "docker ps -a" and see CONTAINER ID for mongo container
    - copy "IPAddress" value to clipboard: "172.19.0.2" for example
    - go to project's config/config.php and replace "172.19.0.2" with your value (this is for the mongodb to allow your connection)

- run "docker-compose down" to stop containers when you finish your work

## Usage

Usage:
- open postman
- to create dummy user in the database execute get request: http://phalcon.api/create_user 
- now use something like Postman to execute this POST request: 
	- http://phalcon.api/authenticate
	- POST data:
		- email: phalconuser@phalconapi.com
		- password: test123
		* use exactly these credentials since user creation is HARD-CODED in this demo project
	* this procedure authenticates user
	* auth token will be shown, copy it to clipboard (in reality you will want to send or store this token as part of the response)
- to test if authentication mechanics works, use Postman with the following settings:
	- authentication test for valid authentication:
		- GET request to: http://phalcon.api/auth_test
		- create header called "Authorization" and set it's value to "Bearer <token>" where <token> is your token from clipboard
		- make sure header is enabled in postman (checkmark is checked)
		- send the request
		- resonse should be: {
                                        "code": 200,
                                        "status": "success",
                                        "message": "Route only authenticated users has access to!"
                                     }
	- authentication test for invalid authentication:
		- follow scenario above, but this time disable Authorization header (simply not use it)
		- send the request
		- respons should be something like: {"code": 0, "status": "error", "message": "Invalid JWT - Authentication failed!"}
- to run PHPunit tests:
	- start containers (docker-compose up -d)
	- go to your command line and switch to your project directory
	- find name of the container on which PHP is running
	- run: docker exec -it <container_name> bash
	- now go to "/project/tests" directory (or any other name you use in variables.env) and execute phpunit
	  * looks something like this: "root@e242331f2013:/project/tests# phpunit"
	  * as a result should see something like: "OK (4 tests, 10 assertions)"

## Links

[:release:]:   https://github.com/phalcon/phalcon-compose/releases
[:status:]:    https://travis-ci.org/phalcon/phalcon-compose
[:phalcon:]:   https://github.com/phalcon/cphalcon
[:downloads:]: https://packagist.org/sergeyklay/phalcon-compose
[:docker:]:    https://www.docker.com
[:compose:]:   https://phalcon-compose.readme.io
[:license:]:   https://github.com/phalcon/phalcon-compose/blob/master/LICENSE.txt
