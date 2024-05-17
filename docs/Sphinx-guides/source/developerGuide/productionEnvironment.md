# Production Environment

## Production Environment Information
The PKP hosting server manages all IQSS owned OJS instances. The OJS production and sandbox instances are deployed as folders within the IQSS account home directory.

 - Sandbox folder: ``iqss.sandbox.sfulib8.publicknowledgeproject.org``
 - Production folder: ``pre-review.iq.harvard.edu``

There are other OJS installation folders within the IQSS account home directory. These are PKP prototypes and can be ignored.

This is the PKP folder structure for the OJS installations:
 - ``cgi-bin``: not used.
 - ``logs``: log files location.
 - ``www``: source code location.

Main log files:
 - ``logs/secure-access_log``: Apache access logs for our instance.
 - ``logs/secure-error_log``: Standard log messages from the OJS application and PPR plugin.

Logs are daily gzipped into the monthly file, eg: ``2024-04-secure-error_log.gz``

To access the gzipped logs, use ``zless``
```
zless logs/2024-04-secure-error_log.gz
```

## Production Database

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