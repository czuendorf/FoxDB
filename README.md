FoxDB
=====

FoxDB is an easy way to store and read JSON objects into a MySQL Database.
It is written in PHP and uses the Slim Framework.

Installation
-----------

Just place it into your PHP-compatible server.

Database configuration
----------------------

To add your database credentials, please create a `db.ini` into the folder `/server`:

    [dbparams]
    dbhost = your.host.name
    dbuser = your_db_user
    dbpass = your_db_pass
    dbname = database_name

Usage
-----

Imagine your have an entity called `cars` with the properties `brand` and `type`.

You want to use it in your front-end application as a JSON object:

    {
        "cars" : 
        {
            "brand" : "Honda",
            "type" : "Civic"
        }
    }

You want to store this entity to a MySQL database, but you do not like to write a lot of SQL statements for this task.

FoxDB helps you to store, update, load and delete your entity objects defined in JSON maps.

Creation of MySQL table for your entity
---------------------------------------

For the Storage of your `cars` entity you need of course a table in your MySQL database. You need to create one with this command:

    CREATE TABLE cars 
    (
        id INT NOT NULL AUTO_INCREMENT, 
        PRIMARY KEY(id),
        brand VARCHAR(30),
        type VARCHAR(30)
    )


FoxDB assumes that a MySQL table exists, which is called like the entity you want to manage. For each property of your command, you need  one according column in your table.

Store an entity
---------------

Assuming you want to create a web application for managing your cars:

    var entity = {
        "cars": {
            "brand": "Honda",
            "type": "Civic"
        }
    };

You could use jQuery for storing your `cars` entity via AJAX.

    $.ajax({
        url: serverURL + "/put",
        dataType: 'json',
        data: JSON.stringify(entity),
        type: "PUT",
        dataType: 'json',
        success: function(result) {
            console.log("success");
        },
        fail: function(result) {
            console.log("fail");
        }
    });

After this AJAX call your entity should be stored in your MySQL `cars` table.

Contributing
------------

1. Fork it.
