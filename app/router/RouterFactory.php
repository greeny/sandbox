<?php

namespace App;

use Nette\Application\IRouter;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;


class RouterFactory
{

	/**
	 * @return IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;
		$router[] = new Route('<presenter>/<action>[/<id>]', array(
			'module' => 'Public',
			'presenter' => 'Dashboard',
			'action' => 'default',
		));
		return $router;
	}

}
