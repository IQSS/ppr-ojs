# Environment Setup

## Post install tasks
After the initial installation of OJS, we need to create a basic OJS configuration to start using the system.

Create a journal. OJS supports multiple journals within a single installation. We need to create one journal.
- [Sample journal settings](docs/ojs-create-journal.png)

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

## Clean the data directories
To start the OJS application fresh, you will need to clean the DB and OJS files within the data directory.

Execute the following target to delete all data files:
``make clean``

## Add admin permissions to a user
In order to grant admin permissions to a user, we need to add the user to the ``admin`` and  ``journal manager`` groups.
The ``admin`` group is group_id = 1 and the ``journal manager`` group is group_id = 2.

Verify group ids
```
SELECT * from user_group_settings WHERE setting_value in ('Site Admin', 'default.groups.name.manager');
```

Get the user id
```
SELECT * from users WHERE username = 'username';
```

Verify what groups the user belongs to
```
SELECT * from user_user_groups WHERE user_id = user_id;
```

Add the admin groups to the user
```
INSERT INTO user_user_groups VALUES (1, user_id);
INSERT INTO user_user_groups VALUES (2, user_id);