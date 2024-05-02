# Containers

## Local Environment
The ``environments`` folder contains the Docker configuration to run the OJS application locally
mounting all the plugin directories in the appropriate OJS folders.

The ``environments/data/db`` stores the DB data and log files.

The ``environments/data/ojs`` stores the OJS files, logs, and source code

## Build the OJS Docker images
Although PKP provides a set of Docker images to run the OJS application, we have extended the images to add support
for XDebug and to mount the OJS application code into the ``environment/data/ojs/src`` folder.

Build the OJS Docker image with XDebug support for OJS version 3.3:
``make docker``

Build the OJS Docker image with XDebug support for OJS version 3.4:
``make docker34``

## Start the OJS application
The first time we start the OJS application, the ojs-entry-point script will copy the source code into the ``environment/data/ojs/src`` folder.
This will take around 5 minutes to complete. After the copy is made, the script will start the Apache server. In subsequent runs, the copy is bypassed on startup.

The OJS application will be ready when you the see the message: ``STARTING OJS...`` in the console log.

To start the environment for OJS version 3.3, execute:
``make``

To start the environment for OJS version 3.4, execute:
``make dev34``

Access the application at http://localhost:8080

Follow the on-screen instruction for the one time installation. More information [https://docs.pkp.sfu.ca/admin-guide/3.3/en/install](https://docs.pkp.sfu.ca/admin-guide/3.3/en/install)

 - Create admin account
 - Primary locale english, no need for additional locales
 - Client character set, connection character set, and File settings defaults are good.
 - DB settings:
    ```
    Driver: [MySQL]
    Host: db
    Username: ojs
    Password: ojs
    Database: ojs
    ```
 - OAI settings and beacon defaults are good.
 ![Sample installation settings](/img/ojs-installation-settings.png)