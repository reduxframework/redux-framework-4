#!/bin/bash

cd "$(dirname "$0")"
cd ../build
echo $(pwd)
git clone --depth 1 git@github.com:reduxtemplates/reduxtemplates.git --branch master master
mv master/.git reduxtemplates/
cd reduxtemplates
git add -A
git commit -m "Release"
git push origin master
