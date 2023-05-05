# Peer Pre-Review Program OJS Customizations Plugin
OJS plugin to implement the customizations for the Harvard Peer Pre-Review Program.

## Production Release
In order to deploy into the PKP servers, we need to create ``tar.gz`` file with the ``pprOjsPlugin`` folder and
send it to PKP support: ``support@publicknowledgeproject.org``. They will review the code and deploy the plugin.

We use the ``./VERSION`` file to generate the release version number.
This version number is used in the plugin ``pprOjsPlugin/version.xml`` file and in the generated ``tar.gz`` artifact.

To create a release, execute the target:
```
make release
```

## Installation
Deploy the plugin root folder ``pprOjsPlugin`` into the ``generic`` plugins folder of the OJS installation.

This is typically ``ojs/plugins/generic``

## Local Environment
The ``environments`` folder contains the Docker configuration to run the OJS application locally mounting the ``pprOjsPlugin`` directory as a generic plugin.

The ``environments/data/db`` stores the DB data and log files.

The ``environments/data/ojs`` stores the OJS files, logs, and source code

### Build the OJS Docker images
Although PKP provides a set of Docker images to run the OJS application, we have extended the images to add support
for XDebug and to mount the OJS application code into the ``environment/data/ojs/src`` folder.

Build the OJS Docker image with XDebug support for OJS version 3.3:
``make docker``

Build the OJS Docker image with XDebug support for OJS version 3.4:
``make docker34``

### Start the OJS application
The first time we start the OJS application, the ojs-entry-point script will copy the source code into the ``environment/data/ojs/src`` folder.
This will take around 5 minutes to complete. After the copy is made, the script will start the Apache server. In subsequent runs, the copy is bypassed on startup.

The OJS appliaction will be ready when you the see the message: ``STARTING OJS...`` in the console log.

To start the environment for OJS version 3.3, execute:
``make``

To start the environment for OJS version 3.4, execute:
``make dev34``

### Clean the data directories
To start the OJS application fresh, you will need to clean the DB and OJS files within the data directory.

Execute the following target to delete all data files:
``make clean``

## Technical Notes

### Create a link to the workflow tab
```
{capture assign=reviewPageUrl}{url router=$smarty.const.ROUTE_PAGE page="workflow" op="index" path=[$submission->getId(), 3] escape=false}{/capture}
```
