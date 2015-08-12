# The Problem
"Be one with the problem, young Skywalker"

## Installation and Usage

_The Problem_ is written in PHP with MySQL and uses some Apache-specific features, and as a result you will need an Apache/PHP/MySQL stack. We recommend installing [XAMPP](https://www.apachefriends.org/index.html).

To run _The Problem_, you will also need the [Imagick](http://php.net/manual/en/book.imagick.php) PHP extension installed. For a guide on how to install this on Windows with XAMPP, take a look [here](http://stackoverflow.com/a/21084043/1629802).

Once both of these are installed, you now need to clone _The Problem_ and copy it into XAMPP. To do this, you will need [Git](https://git-scm.com/) installed. First, change directory to the `htaccess` folder in your XAMPP installation, or the public folder for a different type of installation. Now, run the following command:

```shell
git clone https://github.com/The-Problem/The-Problem
```

_The Problem_ will now be cloned into a `The-Problem` directory inside your public directory.

The installation contains two directories: `www` and `server`. Move the files in `www` to the place that you want _The Problem_ to be accessible from (e.g. in the server's public directory). Move the files in `server` to a folder for _The Problem_ that is preferably in a place inaccessible from the Internet, for example in the XAMPP installation folder. Now, open up the `index.php` file in the copied `www` folder, and change the path to point to the `core/lime.php` in the server directory.

The next step in setting up _The Problem_ is to choose your profile. In this case, 'profile' refers to a set of configuration options that _The Problem_ loads on startup. Specifically, this is the running environment, cache options, and other important options. There are two 'example' files, one for a development environment, and one for a production environment. Unless you know what you are doing, you should copy the `profile-production.php` file in the server directory to `profile.php`. Without having a `profile.php` file, _The Problem_ will not work.

The next step is to setup all of the database tables and configuration. A `database.sql` file is available in the cloned directory. Create a new database for _The Problem_, and run this SQL in it (note: this can be done through the 'Import' feature in phpMyAdmin if that is installed). You may also want to create a user account with permissions for this database. Now, go to the `packages/limecore/libraries/connection/connection.php` file in the server folder, and change the values for the `host`, `username`, `password`, and `database` to correspond with those for your database.

Now, start up Apache and navigate to the directory where you placed the `www` files, in your browser. You should be greeted with _The Problem_'s homepage. Congratulations! You've successfully setup _The Problem_.

Thank you for participating in this computer-aided enrichment centre activity. We look forward to seeing you for the next round of testing.
