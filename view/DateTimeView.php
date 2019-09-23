<?php

namespace view;
class DateTimeView
{


	public function show()
	{
		$timeString = date('l, \t\h\e jS \o\f F o, \t\h\e \t\i\m\e \i\s G:i:s');

		return '<p>' . $timeString . '</p>';
	}
}

