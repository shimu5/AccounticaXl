#!/bin/bash
clear
#echo "ps aux | grep '[p]hp' | grep -r $1 | grep -v grep | awk '{print $2}'"
kill $(ps aux | grep '[p]hp' | grep -r $1 | grep -r $2 | grep -v grep | awk '{print $2}')
#printf 'text\n' > /var/www/html/tem_file.txt
#echo $1
# echo 'new\n'
#echo grep -r $1

