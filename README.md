# Peer Pre-Review Program OJS Customizations Plugin
OJS plugin to implement the customizations for the Harvard Peer Pre-Review Program.

## Production Release
In order to deploy into the PKP servers, we need to create ``tar.gz`` file with the ``ppr-ojs-plugin`` folder and send it to PKP support.

The ``make release`` target will create the tar.gz into the releases folder
```
make release
```

## Installation
Deploy the root folder ``pr-ojs-plugin`` into the ``generic`` plugins folder of the OJS installation.

This is typically ``ojs/plugins/generic``

## Local Environment

### Run OJS Locally
The environments folder contains the Docker configuration to run the OJS application locally mounting the ``ppr-ojs-plugin`` directory as a generic plugin.

Build the OJS Docker image with XDebug support:
``make docker``

To start the environment, execute:
``make``

## Technical Notes

### Create a link to the workflow tab
```
{capture assign=reviewPageUrl}{url router=$smarty.const.ROUTE_PAGE page="workflow" op="index" path=[$submission->getId(), 3] escape=false}{/capture}
```
