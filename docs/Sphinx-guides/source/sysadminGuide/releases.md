# Making a release

## Making a Build

Inside the GitHub actions, there is a release workflow that can only be run manually at the moment, this workflow will execute the following actions:

- Increase the version number on the ``VERSION`` file by a minor release
- Update the version number and the date on ``version.xml``
- Creates the zip file and commits the changes
- Executes ``make new_version``
- After it is done the artifact is uploaded on the action as ``plugin-artifacts`` with an expiration of 7 days.

Currently, both plugins are generated and need to be released simultaneously.

The ``plugin-artifacts`` file will contain the following files: 

- ``pprOjsPlugin-VERSION.tar.gz`` 
- ``pprReviewsReportPlugin-VERSION.tar.gz``

## Application deployment

Both plugins **need to be upgraded simultaneously**, you can't have different versions of them even if there are no changes on one of them.

### UI based deployment

To deploy the artifact file on the OJS installation you will find the option under ``Upgrade > Upload file`` located on:

- ``Settings > Website > Plugins > Generic Plugins > IQSS Peer Pre-Review Program Plugin`` 
- ``Settings > Website > Plugins > Report Plugins > IQSS Peer Pre-Review Report``

### Manual deployment

There is another option to deploy the application by copying the files on the `plugins` directory and decompressing the contents. 

*This is the preferred method by the PKP staff*

## Version Verification

You can verify the installed version of your plugin by going to:


- ``https://SERVER_URL/plugins/generic/pprOjsPlugin/version.xml``
- ``https://SERVER_URL/plugins/reports/pprReviewsReportPlugin/version.xml``

Another way to check the versions and release dates of the plugins is within the database on the ``versions`` table.
