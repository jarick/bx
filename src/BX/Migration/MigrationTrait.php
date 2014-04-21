<?php namespace BX\Migration;
use BX\Base\DI;
use BX\Migration\MigrateManager;

trait MigrationTrait
{
	/**
	 * Get migration manager
	 * @param string $package
	 * @param string $service
	 * @return Manager\MigrateManager
	 */
	public function migrate($package,$service)
	{
		if (DI::get('migration') === null){
			DI::set('migration',new MigrateManager($package,$service));
		}
		return DI::get('migration');
	}
}