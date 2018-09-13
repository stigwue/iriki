<?php

namespace iriki\engine;

/**
* Iriki Generator utility for randomization.
*
*/
class generator
{
    private static $_generator = null;
    private static $_length = 5;
    private static $_deck = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	//length

	//deck
	public static function getSample()
	{
		$sample = Self::$generator->generateString(Self::$_length, Self::$_deck);

		return $sample;
	}
}

?>