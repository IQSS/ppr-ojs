# Deployment

## Production Release
In order to deploy into the PKP servers, we need to create ``tar.gz`` file with the plugin folder and
send it to PKP support: ``support@publicknowledgeproject.org``. They will review the code and deploy into the production servers.

We use the ``./VERSION`` file to generate the release version number.
This version number is then used in the plugin ``<plugin>/version.xml`` file and in the generated ``tar.gz`` artifact name.

To create a release, execute the Makefile target:
```
make release
```
This will increase the version number, update the PPR plugins version data, and create the ``tar.gz`` files under the project ``releases`` folder.

## Production Deployment
Use the OJS ``Upload A New Plugin`` feature in the ``Website > Plugins`` section for the first time deployment.

To deploy upgrades, select the ``upgrade`` function within the deployed plugin submenu.

In both cases, you need to upload the ``tar.gz`` file with the plugin contents created using the ``make release`` target mentioned before.

## Manual Deployment
Copy or mount the plugin's root folder into the OJS plugins directory.

To deploy the ``pprOjsPlugin`` plugin, copy/mount root folder ``pprOjsPlugin`` into the ``generic`` plugins folder of the OJS installation.

This is typically ``ojs/plugins/generic``

To deploy the ``pprReportPlugin`` plugin, copy/mount root folder ``pprReportPlugin`` into the ``reports`` plugins folder of the OJS installation.

This is typically ``ojs/plugins/reports``
