# Local Environment
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

## Start the OJS application locally
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

```{dropdown} Sample installation settings
[![Sample installation settings](/img/ojs-installation-settings.png)](/img/ojs-installation-settings.png)
```


## Post install tasks
After the initial installation of OJS, we need to create a basic OJS configuration to start using the system.

Create a journal. OJS supports multiple journals within a single installation. We need to create one journal.

```{dropdown} Sample journal settings
[![Sample journal settings](/img/ojs-create-journal.png)](/img/ojs-create-journal.png)
```

Enable the PPR plugins. These are disabled by default.
- To enable the PPR plugin => ``Website > Plugins > Generic Plugins > IQSS Peer Pre-Review Program Plugin``
- To enable the PPR Report plugin => ``Website > Plugins > Report Plugins > IQSS Peer Pre-Review Reviews Report``

Edit the PPR plugin settings to enabled all the features:
- ``Website > Plugins > Generic Plugins > IQSS Peer Pre-Review Program Plugin  > Settings``
- ``Website > Plugins > Generic Plugins > IQSS Peer Pre-Review Reviews Report  > Settings``

Reload scheduled tasks for the Acron pluging. The Acron plugin is responsible for executing and managing the OJS scheduled tasks
- ``Website > Plugins > Generic > Acron Plugin > Reload Scheduled Tasks``


## Configure the local SMTP server
After the initial setup, we need to configure OJS to use the local SMTP server to send emails.
The local SMTP server is deployed in the container named smtp. This is as well the host to access it.

Manually edit the deployed OJS configuration under: ``environment/data/ojs/src/config.inc.php``
Under the ``email`` section, ensure the following settings are configured:
```
[email]
smtp = On
smtp_server = smtp
smtp_port = 25
smtp_suppress_cert_check = On
```

#### Setting up a systemd service and timer to restart the SMTP service automatically if it goes down:

Copy ppr-ojs/environment/monitoring/monitor-smtp.service and monitor-smtp.timer to /etc/systemd/system/ on the machine the smtp server is running

Copy ppr-ojs/environment/monitoring/smtp-monitor.sh to your home directory on the machine the smtp server is running and give it execution permissions

The default directory is /home/core/smtp-monitor.sh but can be changes by editing the monitor-smtp.service file

Start the service:
```
sudo systemctl start monitor-smtp.timer
```
A log will be generated and can be viewed by:
```
cat /tmp/smtp-monitor
```

## Clean the data directories
To start the OJS application fresh, you will need to clean the DB and OJS files within the data directory.

Execute the following target to delete all data files:
``make clean``