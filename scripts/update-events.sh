#!/bin/sh

cd "$(dirname $0)/.."

./framework/sake dev/tasks/UpdateEventsTask "flush=1" | tee -a ./logs/update-events.log
