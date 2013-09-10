#!/bin/zsh
ssh marty 'osascript /Users/awross/Desktop/halo.applescript';
while [[ `php n_data.php` ==  `php o_data.php` ]] ; do
	echo "List not yet updated -- \n"
	sleep 3
	ssh marty 'osascript /Users/awross/Desktop/halo.applescript';
done
cp /work/live/halo/data.txt /work/live/halo/old_data.txt
