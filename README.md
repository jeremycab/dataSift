#Test DataSift : Job Queue
## Requirements
This is my project for the DataSift test "Job Queue".
### Install dependencies
The external dependencies used by this project are :
 * PHPUnit version 3.7
 * zendframework/zend-config : 2.0.*
 * zendframework/zend-console : 2.0.*

In order to test the application, use composer to install those dependencies.  
### PHP libraries
The PHP libraries required to run the project are :
 * Semaphore : http://fr2.php.net/manual/en/intro.sem.php
 * Process control : http://fr2.php.net/manual/en/book.pcntl.php

Check that those libraries are installed and activated on your system.
## Working instructions
### Configuration file
Find the configuration file in the repository "app/config".
The parameters required are :
 * nb_workers : the number of workers launched 
 * timeout : the time between keep-alive messages from worker
 * server_adress : the domain used by the manager to create connexion with clients
 * server_port ; the port used by the manager to create connexion with clients
 * nb_clients_max = 10 ; the maximum number of connexion  
 * path_log_file : file where the logs will be written
### Usage
#### Tasks
The task processed by the workers encrypt in MD5 the data sent.
#### Daemon
Start to execute the file "daemon.php". This script launch the workers and is listening for clients connections.
```html
php daemon.php
```
#### Client
In order to send data to the workers, run the script "TaskCommand.php" and follow the inscriptions in the console.
```html
php src/TestBundle/Command/TaskCommand.php
```
#### Monitoring
You can monitor the application by reading the log file configured in "config.ini".
### Unit tests
Use PHPUnit to run the unit tests
```html
phpunit -c phpunit.xml 
```

