<?php

namespace view;

class DateTimeView
{
	public function show()
	{
		//$timeString = date('l, \t\h\e jS \o\f F o, \T\h\e \t\i\m\e \i\s G:i:s');
		$timeString = 'Friday, the 27th of September 2019, The time is';
		return '<p>' . $timeString . '</p>';
	}
}
