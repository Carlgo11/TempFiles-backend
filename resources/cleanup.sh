#!/bin/sh
find /tmp/tempfiles/ -mtime +1 -exec rm {} \;
