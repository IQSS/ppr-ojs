# Installation

## Default Instalation
Use the OJS ``Upload A New Plugin`` feature in the ``Website > Plugins`` section for the first time deployment.

To deploy upgrades, select the ``upgrade`` function within the deployed plugin submenu.

In both cases, you need to upload the ``tar.gz`` file with the plugin contents created using the ``make release`` target mentioned before.

## Manual Installation
Copy or mount the plugin's root folder into the OJS plugins directory.

To deploy the ``pprOjsPlugin`` plugin, copy/mount root folder ``pprOjsPlugin`` into the ``generic`` plugins folder of the OJS installation.

This is typically ``ojs/plugins/generic``

To deploy the ``pprReportPlugin`` plugin, copy/mount root folder ``pprReportPlugin`` into the ``reports`` plugins folder of the OJS installation.

This is typically ``ojs/plugins/reports``
