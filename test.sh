#!/bin/bash

npm run test

cp -v spec/data/make-sql/cameroun.got ../cameroun-server/src/tfw/pri/install.sql
cp -v spec/data/make-php/cameroun.got.php ../cameroun-server/src/tfw/data.php


