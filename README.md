# Peer Pre-Review Program OJS Customizations Plugin
OJS plugin to implement the customizations for the Harvard Peer Pre-Review Program.

## Production Release
In order to deploy into the PKP servers, we need to create ``tar.gz`` file with the ``ppr-ojs-plugin`` folder and send it to PKP support.

The ``make release`` target will create the tar.gz into the releases folder
```
make release
```

## Installation
Deploy the plugin root folder ``pprOjsPlugin`` into the ``generic`` plugins folder of the OJS installation.

This is typically ``ojs/plugins/generic``

## Local Environment

### Run OJS Locally
The environments folder contains the Docker configuration to run the OJS application locally mounting the ``pprOjsPlugin`` directory as a generic plugin.

Build the OJS Docker image with XDebug support for OJS version 3.3:
``make docker``

Build the OJS Docker image with XDebug support for OJS version 3.4:
``make docker34``

To start the environment for OJS version 3.3, execute:
``make``

To start the environment for OJS version 3.4, execute:
``make dev34``

## Technical Notes

### Create a link to the workflow tab
```
{capture assign=reviewPageUrl}{url router=$smarty.const.ROUTE_PAGE page="workflow" op="index" path=[$submission->getId(), 3] escape=false}{/capture}
```
