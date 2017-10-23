#!/bin/sh

cd "$(dirname $0)/.."

./framework/sake dev/build "flush=1"
