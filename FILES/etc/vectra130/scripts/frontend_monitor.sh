#/bin/sh

# This script monitors if a dvb device's frontend is opened by VDR.
# Can be used with a single tuner or with two tuners.
# Output:
#  1  - adapter 0 is on
#  2  - adapter 1 is on
#  3  - both are on
#  -  - both are off
#  .  - VDR is not running

# VDR executable. Change to vdr.bin for OpenELEC.
VDR=vdr

while true; do
    vdrpid=$(pidof $VDR)
    if [ -z "$vdrpid" ]; then
        printf '.'
    else
        mask=0
        for adapter in 0 1 2 3 4; do
            if sudo lsof -p $vdrpid 2>/dev/null | grep -q "adapter$adapter/frontend"; then
                mask=$(($mask + $adapter + 1))
            fi
        done
        if [ $mask -eq 0 ]; then
            printf '-'
        else
            printf $mask
        fi
    fi
    sleep 1
done
