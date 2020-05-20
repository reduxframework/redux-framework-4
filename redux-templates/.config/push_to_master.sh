#!/bin/bash

cd "$(dirname "$0")"
cd ../build
echo $(pwd)
git clone --depth 1 git@github.com:starterblocks/starterblocks.git --branch master master
mv master/.git starterblocks/
cd starterblocks
git add -A
git commit -m "Release"
git push origin master
