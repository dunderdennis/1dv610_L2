<?php

namespace view;

class RMCalcView
{
	const viewID = 'RMCalcView';

	private static $weight = 	self::viewID . '::Weight';
	private static $reps = 		self::viewID . '::Reps';
	private static $submit = 	self::viewID . '::Submit';

	public function getHTML(): string
	{
		$ret = "<h3>1RM Calculator</h3>
		<a href='https://en.wikipedia.org/wiki/One-repetition_maximum'>What's a 1RM? (wikipedia)</a>
		<br><br>";

		$ret .= $this->getRMCalcFormHTML();

		if ($this->postIsSet()) {
			$weight = $this->getPostWeight();
			$reps = $this->getPostReps();

			$ret .= $this->getTableHTML($weight, $reps);
		}

		return $ret;
	}

	public function getPostWeight(): string
	{
		return $_POST[self::$weight];
	}

	public function getPostReps(): string
	{
		return $_POST[self::$reps];
	}

	public function postIsSet(): bool
	{
		return (isset($_POST[self::$reps]) && $_POST[self::$weight]);
	}

	private function getRMCalcFormHTML(): string
	{
		return '
		<form method="post"> 
			<fieldset>
				<legend>Enter weight and repetitions</legend>

				<label for="' . self::$weight . '">Weight :</label>
				<input type="text" id="' . self::$weight . '" name="' . self::$weight . '"/>

				<label for="' . self::$reps . '">Reps :</label>
				<input type="password" id="' . self::$reps . '" name="' . self::$reps . '" />
				
				<input type="submit" name="' . self::$submit . '" value="Submit" />
			</fieldset>
		</form>
		<br>
	';
	}

	private function getTableHTML($weight, $reps): string
	{
		$ret = '<table>';

		$tableContent = '';

		if ($weight > 0 && $reps > 0) {
			// Using the Brzycki formula, which can be found in the linked wikipedia page
			$RM = $weight * (36 / (37 - $reps));
			$RMC = 1;
			$RMreps = [1, 2, 4, 6, 8, 9, 12, 16, 20, 24, 30];
			$percent = 100;

			$ret .= '
			<tr>
				<th>Percentage of 1RM</th>
				<th>Lifted Weight</th>
				<th>Repetitions</th>
			</tr>
			';

			for ($x = 0; $x < 11; $x++) {
				$tr = '<tr>';
				for ($y = 0; $y < 3; $y++) {
					$td = '<td>';
					switch ($y) {
						case 0:
							$td .= $percent . '%';
							$percent -= 5;
							break;
						case 1:
							$td .= round($RM * $RMC) . ' kg';
							$RMC -= 0.05;
							break;
						case 2:
							$td .= $RMreps[$x];
							break;
					}
					$td .= '</td>';
					$tr .= $td;
				}
				$tr .= '</tr>';
				$tableContent .= $tr;
			}
		}
		$ret .= $tableContent;
		$ret .= '</table>';
		return $ret;
	}
}
