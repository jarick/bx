<?php namespace BX\Validator;
use BX\Validator\Manager\Validator;

trait ValidatorTrait
{
	/**
	 * Get validator
	 * @param array $aRules
	 * @param array $aLabels
	 * @param array $bNew
	 * @return Validator
	 */
	protected function validator($aRules,$aLabels,$bNew = true)
	{
		return Validator::getManager(false,[
				'labels' => $aLabels,
				'rules'	 => $aRules,
				'new'	 => $bNew,
		]);
	}
}