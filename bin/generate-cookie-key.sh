#!/usr/bin/env sh

set -e

bin="`dirname "$0"`"
root="$bin/.."

name="`basename "$(realpath "$root")"`"
user="${FLUX_PUBLISH_DOCKER_USER:=fluxfw}"
image="$user/$name"
tag="`get-release-tag "$root"`"

docker run --rm --network none --entrypoint "/$name/bin/generate-cookie-key.mjs" "$image:$tag" "$@"
