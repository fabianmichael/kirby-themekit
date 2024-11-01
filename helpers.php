<?php

function themes(bool $return = true): ?string
{
	return snippet('themes', return: $return);
}
