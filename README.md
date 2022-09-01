# Origin Backend Take Home Assignment
You can check the original [Read.Me](https://github.com/OriginFinancial/origin-backend-take-home-assignment) for all original project requirements. 

## Setup

#### Requirements
* [Docker](https://www.docker.com/)

#### Run
* `git clone git@github.com:padovag/origin-backend-take-home-assignment.git`
* `docker-compose up` at project root
* `docker ps` to get the container ID
* `docker exec -it [CONTAINER_ID] bash` to enter the docker container bash to run the remaining commands
* `composer install` in the container project root

There! Now you can make HTTP requests to http://localhost/.

## Test Instructions
* after entering the container bash, run `vendor/bin/phpunit tst` inside it

```
Time: 00:00.059, Memory: 4.00 MB

OK (31 tests, 42 assertions)
```

* make a `POST` HTTP request to `localhost/index.php/risk/calculate`
* request body:

```
{
  "age": 75,
  "dependents": 1,
  "income": 300000,
  "house": {"ownership_status": "mortgaged"},
  "marital_status": "married",
  "risk_questions": [1,1,1],
  "vehicle": {"year": 2022}
}
```

* response:
```
200 OK 22 ms

{
    "auto": "responsible",
    "disability": "ineligible",
    "home": "responsible",
    "life": "ineligible"
}
```



## The Project
This project was built entirely with [PHP](https://www.php.net/), on a [Docker](https://www.docker.com/) container with PHP 7.4 and [composer](https://getcomposer.org/) to manage its dependencies, such as [PHPUnit](https://phpunit.de/).
To keep project as simple as possible, without many external libraries and dependencies, the API was built from scratch along with all controller classes and input validation methods.

### Architecture

```
origin/
    src/
        model/
            House.php
            RiskProfile.php
            RiskScore.php
            RiskScoreToRiskProfileMapper.php
            UserProfile.php
            Vehicle.php
        BaseController.php
        RiskController.php
    index.php
        
```

These are the main project files, where all API and business logic is located. 

To build our API structure in a way that can be easily escalated in the future, we use the classes `index.php`, 
which is used as an entry point to all HTTP requests, filtering them and calling the desired controller method, and the class `BaseController`, 
which is a parent class for all controllers, including the one we added (`RiskController`), and others to be implemented in the future. 
`BaseController` defines a method `processRequest`, which is called from `index.php`, and will decide which actual method to call from the implementation controllers. 
All validation methods are also inside `BaseController`, available to be used in all its child classes. 

As for the business logic and our risk algorithm, it's basically all inside `RiskScore`, in the `calculate()` method. 
This class is responsible for receiving a user profile (represented by `UserProfile`) and calculate its score based on all [rules](https://github.com/OriginFinancial/origin-backend-take-home-assignment#the-risk-algorithm) we've seen. 
The `RiskScore` final object is then mapped to a `RiskProfile`, a class to represent a final profile with its pre-determined definitions instead of dealing with score numbers,
through `RiskScoreToRiskProfileMapper`, a class created to be responsible for the mapping logic, also divided in a way that can be easily tested and changed in the future. 

#### Tests
```
origin/
    tst/
        model/
            RiskScoreTest.php
            RiskScoreToRiskProfileMapperTest.php
        BaseControllerTest.php
        RiskControllerTest.php
```

#### Dependency Management
```
origin/
    vendor/
    composer.json
```

#### Docker files
```
origin/
    docker-compose.yml
    Dockerfile
```
