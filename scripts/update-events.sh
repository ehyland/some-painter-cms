#!/bin/sh

cd "$(dirname $0)/.."

./framework/sake dev/tasks/UpdateEventsTask | tee -a ./logs/update-events.log
