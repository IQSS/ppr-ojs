# Testing 

## Automated testing
There is a suite of automated tests that run automatically in GitHub when a PR is created or after a commit in the ``main`` branch.

The tests used a custom Docker image to execute the tests: ``hmdc/ppr_ojs_test``. More information of how to build this image in the section below.

There is a GitHub action configured on ``.github/workflows/test.yml`` that will execute these tests on commits to ``main`` and on Pull Requests but can also be executed locally with:  

- ``make test`` for the base plugin
- ``make test_report`` for for the report plugin

## Build the PPR test Docker image
Build test image with PHP, PHPUnit and the OJS source code.
``make docker-test``

The Docker image is based on the OJS installation images from https://gitlab.com/pkp-org/docker/ojs

The definition of the PPR test Docker image is located under: ``environment/Dockerfile.test``

## Test environment

### Test environment SMTP server
There is a virtual machine running Docker to host the SMTP server or the test environment.

This is the Docker command use to run the SMTP server using MailDev:
```
docker run --rm -itd --name iqss-smtp-ojs -p 1080:1080 -p 1025:1025 \
  --env MAILDEV_SMTP_PORT=1025 --env MAILDEV_WEB_PORT=1080 --env MAILDEV_MAIL_DIRECTORY=/mail \
  --mount type=tmpfs,destination=/mail maildev/maildev:2.0.5
```

Stop the SMTP server with the following command:
```
docker rm -f iqss-smtp-ojs