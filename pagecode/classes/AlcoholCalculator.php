<?php

class AlcoholCalculator
{
	var $commonFactors = array(

		"banquetBuffet" => array(
			"banquet"  => 1.25,
			"buffet" => 1.00
		),

		"eventType" => array(
			"presentation" => 0.8,
			"corporative"  => 1.0,
			"reception"    => 0.9
		),

		"duration" => array(
			2 => 0.9, 3 => 1.0, 4 => 1.1, 5 => 1.25, 6 => 1.4, 7 => 1.55, 8 => 1.7, 9 => 1.85, 10 => 2
		),

		"areaType" => array(
			"open"       => 1.1,
			"closed"     => 1.0,
			"restaraunt" => 1.2,
			"club"       => 1.2
		)
	);

	var $specificFactors = array(
		"alcoholDrinks" => array(
			"season" => array(
				"winter" => 1.2,
				"spring" => 1.1,
				"summer" => 1.0,
				"autumn" => 1.1
			)
		),

		"softDrinks" => array(
			"season" => array(
				"winter" => 1.0,
				"spring" => 1.1,
				"summer" => 1.5,
				"autumn" => 1.1
			)
		)
	);

	var $drinksFactors1 = array(
		"alcoholDrinks" => array(
			"wine"      => 0.70,
			"champagne" => 0.75,
			"cognac"    => 0.15,
			"vodka"     => 0.20,
			"cocktail"  => 0.60
		),
		"softDrinks" => array(
			"juice" => 0.6,
			"water" => 0.6
		)
	);

	var $drinksFactors2 = array(
		"alcoholDrinks" => array(
			"wine"      => 0.75,
			"champagne" => 0.75,
			"cognac"    => 0.85,
			"vodka"     => 0.80,
			"cocktail"  => 0.60
		),
		"softDrinks" => array(
			"juice" => 0.6,
			"water" => 0.6
		)
	);

	var $defaultAlcoholDrinksPackaging = array(
		"wine"      => 0.75,
		"champagne" => 0.75,
		"cognac"    => 0.50,
		"vodka"     => 1.00,
		"cocktail"  => 0.20
	);

	var $defaultSoftDrinksPackaging = array(
		"juice" => 1.00,
		"water" => 0.50
	);

	var $alcoholDrinksNames = array(
		"wine"      => "вино",
		"champagne" => "шампанское",
		"cognac"    => "коньяк",
		"vodka"     => "водка",
		"cocktail"  => "коктейли"
	);

	var $softDrinksNames = array(
		"juice" => "сок",
		"water" => "питьевая вода"
	);

	function calc($type, $guestsCount,
		$drinksUsage, $drinksPackaging,
		$commonFactors, $specificFactors)
	{
		$tmp1 = $drinksUsage;

		$keys = array_keys($drinksUsage);
		foreach($keys as $key)
		{
			$tmp1[$key] *= $guestsCount;
			$tmp1[$key] *= $this->drinksFactors1[$type][$key];

			foreach($commonFactors as $name => $value)
			{
				$tmp1[$key] *= $this->commonFactors[$name][$value];
			}

			foreach($specificFactors as $name => $value)
			{
				$tmp1[$key] *= $this->specificFactors[$type][$name][$value];
			}
		}

		$tmp2 = array();
		$keys = array_keys($drinksUsage);
		$keys2 = array_keys($drinksUsage);
		foreach($keys as $key)
		{
			$summ1 = 0;
			$summ2 = 0;
			foreach($keys2 as $key2)
			{
				$summ1 += $tmp1[$key2];
				if($key2 != $key)
				{
					$summ2 += $tmp1[$key2];
				}
			}

			$tmp2[$key] = $tmp1[$key] - ($tmp1[$key] / $summ1 * $summ2 * $this->drinksFactors2[$type][$key]);
			$tmp2[$key] /= $drinksPackaging[$key];
		}

		$tmp2 = array_map("round", $tmp2);

		return $tmp2;
	}

}

?>