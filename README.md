# PHP PDO Database Library

This is just a simple Database Library with use of Prepared Statements and custom Methods for easy Database Communication.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

The PPDL needs PHP (7.3.8 recommended), a Webserver of your Choice and also an Database of your Choice.

<a href="https://www.php.net/downloads.php">PHP 7.3.8 Downloads</a><br>
<a href="https://httpd.apache.org/">Apache Webserver</a><br>
<a href="https://mariadb.org/">MariaDB</a><br>

### Installing (Windows 10)

First you need to install PHP and setup your php.ini to use the PDO-mysql and mysqli plugins.

```
extension=mysqli
extension=pdo_mysql
```

Then you need to install a Webserver of your Choice (Install Instructions vary between the different Webservers).

And then install a Database Program of your Choice (Install Instructions vary between the different Webservers).
Note: We used MariaDB, when using other Database Software as MYSQLi-Compatible ones, you may need to change php plugins and change some database settings in the PPDL.

## Contributing

Please read [CONTRIBUTING.md](https://github.com/GamesGamble/PPDL/blob/master/.github/CONTRIBUTING.md) and our [CODE_OF_CONDUCT.md](https://github.com/GamesGamble/PPDL/blob/master/.github/CODE_OF_CONDUCT.md) for the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/GamesGamble/PPDL/tags). 

## Authors

* **GamesGamble** - *Initial work* - (https://github.com/GamesGamble)

See also the list of [contributors](https://github.com/GamesGamble/PPDL/contributors) who participated in this project.

## License

This project is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International Public License - see the [LICENSE.md](https://github.com/GamesGamble/PPDL/blob/master/LICENSE.md) file for details
