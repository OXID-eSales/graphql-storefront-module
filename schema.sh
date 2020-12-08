#!/bin/bash

if [[ "$TRAVIS_PHP_VERSION" = "7.4" && "$DEPENDENCIES" = "" && $TRAVIS_TAG != "" ]]; then
  echo "Documentation generation will be triggered."
else
  echo "We only trigger documentation generation for specific PHP version on tagged branch."
  exit 0
fi

body='{
"request": {
"branch":"master"
}}'

curl -s -X POST \
   -H "Content-Type: application/json" \
   -H "Accept: application/json" \
   -H "Travis-API-Version: 3" \
   -H "Authorization: token $TRAVIS_API_TOKEN" \
   -d "$body" \
   https://api.travis-ci.com/repo/OXID-eSales%2Fgraphql-base-module/requests
