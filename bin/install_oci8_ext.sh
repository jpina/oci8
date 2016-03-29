#!/bin/bash

BASE_DIR=$(dirname $0)
cd $BASE_DIR

# Install Oracle Instant Client
sudo apt-get update -qq
sudo apt-get -y install -qq build-essential libaio1 unzip wget

sudo mkdir -p /opt/oracle

sudo unzip -o ./instantclient-basic-linux.x64-12.1.0.2.0.zip -d /opt/oracle
sudo unzip -o ./instantclient-sdk-linux.x64-12.1.0.2.0.zip -d /opt/oracle

sudo ln -sf /opt/oracle/instantclient_12_1 /opt/oracle/instantclient
sudo ln -sf /opt/oracle/instantclient/libclntsh.so.12.1 /opt/oracle/instantclient/libclntsh.so
sudo ln -sf /opt/oracle/instantclient/libocci.so.12.1 /opt/oracle/instantclient/libocci.so

sudo bash -c 'echo /opt/oracle/instantclient > /etc/ld.so.conf.d/oracle-instantclient'
if [ $? -ne 0 ]; then exit 1; fi

# Use for PHP 7.x
OCI8_VERSION='2.1.0'
# Use for PHP 5.x
if [[ "1" = "$(php -r "echo version_compare(phpversion(), '7.0.0', '<');")" ]]; then
    OCI8_VERSION='2.0.10'
fi

OCI8_DIRNAME="oci8-$OCI8_VERSION"
OCI8_FILENAME="$OCI8_DIRNAME.tgz"

if [ ! -f "./$OCI8_NAME.tgz" ]; then
    wget -c -t 3 -O ./$OCI8_FILENAME https://pecl.php.net/get/$OCI8_FILENAME
fi

rm -rf ./$OCI8_DIRNAME

# Compile PHP OCI8 extension
tar xvf ./$OCI8_FILENAME
cd $OCI8_DIRNAME
phpize
./configure -with-oci8=shared,instantclient,/opt/oracle/instantclient
make -j"$(nproc)"
sudo make install

cd ..
rm -rf package.xml ./$OCI8_DIRNAME

# Enable extension

# For Ubuntu 13.04 and earlier:
#sudo bash -c 'echo extension=oci8.so > /etc/php5/apache2/conf.d/oci8.ini'
#/etc/init.d/apache2 restart

# For Ubuntu 13.10 and later:
# sudo bash -c 'echo extension=oci8.so > /etc/php5/mods-available/oci8.ini'
# if [ $? -ne 0 ]; then exit 1; fi
# sudo php5enmod oci8

exit $?
