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