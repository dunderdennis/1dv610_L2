<?php

namespace view;

class DateTimeView
{
	public function response()
	{
		$timeString = date('l, \t\h\e jS \o\f F o, \T\h\e \t\i\m\e \i\s G:i:s');

		return '<p>' . $timeString . '</p>';
	}
}
