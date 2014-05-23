<?php namespace BX\Migration;
use BX\Config\DICService;
use BX\Migration\MigrateManager;

trait MigrationTrait
{
	/**
	 * Return migration manager
	 *
	 * @param string $package
	 * @param string $service
	 * @return Manager\MigrateManager
	 */
	public function migrate($package,$service)
	{
		if (DICService::get('migration') === null){
			$manager = function() use($package,$service){
				new MigrateManager($package,$service);
			};
			DICService::set('migration',$manager);
		}
		return DICService::get('migration');
	}
}