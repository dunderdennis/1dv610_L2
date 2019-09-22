<?php

class DateTimeView
{


	public function show()
	{
		$timeString = date('l, \t\h\e jS \o\f F o, \t\h\e \t\i\m\e \i\s G:i:s');

		// Sunday, the 22nd of September 2019, The time is 16:39:54

		return '<p>' . $timeString . '</p>';
	}
}

