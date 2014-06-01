<?php
require('ShowsProvider.class.php');

$shows_provider = new ShowsProvider('http://www.themurdermysteryco.com/location_m20.php?location=Riverside%2C+CA');
$shows = $shows_provider->getShows();

$available_shows = array();

foreach($shows as $show) {
	if(!$show['soldout'])
		$available_shows[$show['showid']] = $show;
}

heder('Content-type: text/plain');

if(count($available_shows) > 0) {
	echo 'Showtime! :D', PHP_EOL, PHP_EOL;

	foreach($available_shows as $show)
		echo sprintf('%s		%s		%s', $show['date'], $show['time'], $show['theme']), PHP_EOL;
}
else
	echo 'No showtimes available :(', PHP_EOL;
