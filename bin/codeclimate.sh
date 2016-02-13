#!/bin/bash

$(dirname $0)/../vendor/bin/test-reporter --stdout > codeclimate.json
curl -X POST -d @codeclimate.json -H 'Content-Type: application/json' -H 'User-Agent: Code Climate (PHP Test Reporter v0.2.0)' https://codeclimate.com/test_reports
