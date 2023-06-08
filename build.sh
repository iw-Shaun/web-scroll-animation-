#!/bin/bash -x

set -e

VERSION=${1}
BUILD_FOLDER=build
OUTPUT_FOLDER=output

if [[ "${1}" == "" ]]; then
    echo "Missing version argument"
    exit 1
fi

[ -d ${BUILD_FOLDER} ] && rm -rf ${BUILD_FOLDER}
mkdir ${BUILD_FOLDER}

git archive HEAD | tar -x -C ./${BUILD_FOLDER}/

# Build
(cd ${BUILD_FOLDER} && composer install && npm install && npm run build)
(cd ${BUILD_FOLDER} && chmod -R 777 storage bootstrap/cache)

# Create output folder for saving wallpaper.
mkdir ${BUILD_FOLDER}/public/images/output && chmod 777 ${BUILD_FOLDER}/public/images/output

# Pack
[ -d ${OUTPUT_FOLDER} ] && rm -rf ${OUTPUT_FOLDER}
mkdir ${OUTPUT_FOLDER}
(cd ${BUILD_FOLDER} && zip --symlinks -rq ../${OUTPUT_FOLDER}/${VERSION}.zip ./* -x node_modules/\*)
