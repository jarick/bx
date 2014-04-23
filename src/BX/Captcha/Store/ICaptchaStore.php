<?php namespace BX\Captcha\Store;
use \BX\Captcha\Entity\CaptchaEntity;

interface ICaptchaStore
{
	public function getByUniqueId($unique_id);
	public function clear($day);
	public function delete(CaptchaEntity $entity);
	public function create($unique_id);
}