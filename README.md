# ToT Project for CSCI 562, with Clay, Austin, David, and Chris
Link to Index: https://turing.cs.olemiss.edu/~tot/tot/

# Default User Logins - All passwords are "test123"
From DatabaseBootstrap.sql
* Manager Login - Username:testManager

From PopulateTables.sql
* Customer Login - Username:testCustomer
* Waiter Login - Username:testWaiter
* Host Login - Username:testHost

# Setup

We currently don't have a special group enviornment so I won't be specific, but to get the database set up run DatabaseBootstrap.sql, then run PopulateTables.sql     
To change the information for your local DB instance, go into "ToT/Constants.php" and edit these variables to refelect your local setup.
* DB_DEV_USER
* DB_DEV_PASSWORD
* DB_DEV_HOST
* DB_DEV_SCHEMA

If you are using Turing rather than a local dev env, you can change these values
* CLAY_ENVIRONMENT
* CLAY_USER
* CLAY_DB_USER
* CLAY_DB_PASSWORD
* CLAY_DB_SCHEMA

Another note, to connect to your database, do `mysql <Your Username> -p` in PuTTY/command line when SSH'ed into Turing, then from there use the `source` command to run the .sql files! It defaults to using your home directory if you just put filename, so I always just move them to my home before running them. Lazy but works.
# Environment Notes

It is highly recommended you use [Visual Studio Code](https://code.visualstudio.com) with the [PHP Intelephense extension](https://marketplace.visualstudio.com/items?itemName=bmewburn.vscode-intelephense-client).     
the .vs code folder also has relevant settings.
