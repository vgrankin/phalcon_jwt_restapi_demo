# Phalcon REST API (JWT) example

This project utilizes Phalcon PHP framework, MongoDB and JWT firebase library to implement REST API service.
It demonstrates how to implement REST API features and can be used as example REST API project or a starting point.

application/index.php is an entry point of the application and it contains several api examples which 
demonstrate how to authenticate user using JWT (Json Web Token), how to check if user is authenticated (using JWT token) 
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
- install composer on your computer (not docker container)
- Open your hosts file (/etc/hosts) and enter a new line like this:
127.0.0.1 phalcon.api
* if you want something other than phalcon.api then search/replace all the "phalcon.api" entries in this repository
  with the name you like (for example phalcon.local)
- now go to project directory, then go to "application" dir (on your computer) and run "composer install" to install dependencies (see application/composer.json)
- now go to project directory and run "docker-compose build". It will download all images required.
- run "docker-compose up -d" to run containers
    * after that http://phalcon.api should be up and running

- current mongo db hack required to allow mongodb connections:
    - make sure containers are running ("docker-compose up -d")
    - run the following command, where "039" is part of id of running mongo container:
        - docker inspect 039  | grep "IPAddress"
        * to find id of mongo container run "docker ps -a" and see CONTAINER ID for mongo container
    - copy "IPAddress" value to clipboard: "172.19.0.2" for example
    - go to project's config/config.php and replace "172.19.0.2" with your value (this is for the mongodb to allow your connection)

* in case you are on Windows and you have a problem when php container restarts over and over, probably in this case 
  the error is caused due to git usage which (by default) automatically converts LF to CRLF. Status of the container
  will be something like this: "STATUS Restarting (127) Less than a second ago". The error will be something like this:
  "/opt/docker/provision/entrypoint.d/fix-permissions.sh: line 2: $'\r': command not found". This means we need to go to
  docker/app/bin/fix-permissions.sh on Windows (directory to which you cloned this repo) and to use Notepad++ 
  (or other editor you prefer) to replace CRLF to LF (Edit -> EOL Conversion -> Unix LF). After that you may want to 
  stop/remove all containers for this project, delete images and run "docker-compose build" again, then 
  "docker-compose up -d". Now everything should work correctly.

- run "docker-compose down" to stop containers when you finish your work
* additionally look at documentation for the docker-phalcon version of a framework package: https://phalconphp.com/en/download/docker

## Usage

Usage:
- open postman
- to create dummy user in the database execute get request: http://phalcon.api/api/create_user 
- now use something like Postman to execute this POST request: 
	- http://phalcon.api/api/authenticate
	- POST data:
		- email: phalconuser@phalconapi.com
		- password: test123
		* use exactly these credentials since user creation is HARD-CODED in this demo project
	* this procedure authenticates user
	* auth token will be shown, copy it to clipboard (in reality you will want to send or store this token as part of the response)
- to test if authentication mechanics works, use Postman with the following settings:
	- authentication test for valid authentication:
		- GET request to: http://phalcon.api/api/auth_test
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

## Notes

You will still need to:
- restructure urls according to best REST API practices
- use HTTP methods (GET, POST, PUT, DELETE etc.) according to best REST API practices
- send HTTP statuses according to best REST API practices
- implement token refresh
- send or store jwt the appropriate way on client system
- map responses of your API with appropriate HTTP statuses (for example when to send 200 or 500 etc.)
- implement SSL (https) instead of http connection
- reorganize code for your needs and implement new features
* use docker and composer to install additional software required by your project

## Links

- https://phalconphp.com/en/download/docker
- https://www.docker.com
