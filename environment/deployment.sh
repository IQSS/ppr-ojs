#!/bin/bash

echo "Deploying version: $1";
if [ -z "$1" ]; then
    echo "Version number parameter needed, eg: 1.0.0.33"
    exit 1;
fi

echo "Cleanup pprOjsPlugin...";
rm -rf ./pprOjsPlugin.bak
rm -rf ./pprOjsPlugin
tar xzf "pprOjsPlugin-$1.tar.gz"
echo "Deploying...";
if [ -d ./pprOjsPlugin ]; then
    mv ../pre-review.iq.harvard.edu/www/plugins/generic/pprOjsPlugin ./pprOjsPlugin.bak
    mv ./pprOjsPlugin ../pre-review.iq.harvard.edu/www/plugins/generic/
    cd ../pre-review.iq.harvard.edu/www
    php lib/pkp/tools/installPluginVersion.php plugins/generic/pprOjsPlugin/version.xml
    cd ../../deployment
fi

echo "Cleanup pprReviewsReportPlugin...";
rm -rf ./pprReviewsReportPlugin.bak
rm -rf ./pprReviewsReportPlugin
tar xzf "pprReviewsReportPlugin-$1.tar.gz"
echo "Deploying...";
if [ -d ./pprReviewsReportPlugin ]; then
    mv ../pre-review.iq.harvard.edu/www/plugins/reports/pprReviewsReportPlugin ./pprReviewsReportPlugin.bak
    mv ./pprReviewsReportPlugin ../pre-review.iq.harvard.edu/www/plugins/reports/
    cd ../pre-review.iq.harvard.edu/www
    php lib/pkp/tools/installPluginVersion.php plugins/reports/pprReviewsReportPlugin/version.xml
    cd ../../deployment
fi

mysql iqss_prereview -u iqss_prereview -p -e 'SELECT major, build, date_installed, current, product FROM versions'