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

Storing an entity
-----------------

Assuming you want to create a web application for managing your cars:

    var entity = {
        "cars": {
            "brand": "Honda",
            "type": "Civic"
        }
    };

You could use jQuery for storing your `cars` entity via an AJAX call using the `PUT` method.

    $.ajax({
        url: "http://localhost/foxdb/",
        dataType: 'json',
        data: JSON.stringify(entity),
        type: "PUT",
        dataType: 'json'
    });

After this AJAX call your entity should be stored in your MySQL `cars` table.

Deleting an entity
------------------

Deleting an entity is quite as easy as storing one. If you want to delete all entities with a specific brand you could do it like that.

First you will be to defined which entities should be deleted. You can select a subset through the properties of your column.
You could use the `id`, the `brand` or its `type` for sub-selecting.

In this case we will sub-select through the property `brand` which should be `Honda`:

    var targets = {
        "cars": {
            "brand" : "Honda"
        }
    };

Then we will pass this JSON map through an AJAX call again, using the `DELETE` method:

    $.ajax({
        url: "http://localhost/foxdb/",
        dataType: 'json',
        data: JSON.stringify(targets),
        type: "DELETE",
        dataType: 'json'
    });

If you check your MySQL again, the previously stored Honda entity should be removed.

Contributing
------------

1. Fork it.
