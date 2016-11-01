#!/bin/bash
while [ true ]; do
	sleep 10
	if [ $(pidof -xs StreamingServer | wc -w) == 0 ]; then
		if [ $(pidof -xs StreamingServer | wc -w) == 0 ]; then
			StreamingServer -d
		fi
	fi
done;
