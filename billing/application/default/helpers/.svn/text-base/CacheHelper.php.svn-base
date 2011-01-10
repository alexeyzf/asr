<?php
class CacheHelper
{
	public static function getAspCacheParams($content)
	{
		$fh = explode(PHP_EOL, $content);
		$pattern_VIEW = '%<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE"%';
		$pattern_EVENT = '%<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION"%';

		foreach($fh as $row)
		{
  			if(preg_match($pattern_VIEW, $row, $matches_VIEW))
  			{
  				$view = explode("\"", $row);
  			}
  			if(preg_match($pattern_EVENT, $row, $matches_EVENT))
  			{
  				$event = explode("\"", $row);
  			}
		}

		$asp['view']  = $view[7];
		$asp['event'] = $event[7];
		return $asp;
	}

}
?>