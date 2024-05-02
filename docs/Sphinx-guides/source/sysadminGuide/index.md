## System Administrator Guide
In order to deploy into the PKP servers, we need to create ``tar.gz`` file with the plugin folder and
send it to PKP support: ``support@publicknowledgeproject.org``. They will review the code and deploy into the production servers.

We use the ``./VERSION`` file to generate the release version number.
This version number is then used in the plugin ``<plugin>/version.xml`` file and in the generated ``tar.gz`` artifact name.

To create a release, execute the Makefile target:
```
make release
```
This will increase the version number, update the PPR plugins version data, and create the ``tar.gz`` files under the project ``releases`` folder. 

```{toctree}
users
emailTemplates
environments
config
