#
# The Application Core Container
#

# Pull base image
FROM phalconphp/php-apache:ubuntu-16.04

# install phpunit
RUN curl https://phar.phpunit.de/phpunit-6.phar -L > phpunit.phar \
  && chmod +x phpunit.phar \
  && mv phpunit.phar /usr/local/bin/phpunit \
  && phpunit --version

MAINTAINER Serghei Iakovlev <serghei@phalconphp.com>

ENV PROVISION_CONTEXT "development"

# Deploy scripts/configurations
COPY bin/*.sh /opt/docker/provision/entrypoint.d/

RUN \
    # Custom provisions
    chmod +x /opt/docker/provision/entrypoint.d/*.sh \
