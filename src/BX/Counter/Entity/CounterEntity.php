<?php namespace BX\Counter\Entity;
use \BX\Validator\IEntity;

/**
 * @property-read integer $id
 * @property string $entity
 * @property string $entity_id
 * @property string $timestamp_x
 * @property integer $counter
 */
class CounterEntity implements IEntity
{
	use \BX\Validator\EntityTrait,
	 \BX\Translate\TranslateTrait,
	 \BX\Date\DateTrait;
	const C_ID = 'ID';
	const C_ENTITY = 'ENTITY';
	const C_ENTITY_ID = 'ENTITY_ID';
	const C_TIMESTAMP_X = 'TIMESTAMP_X';
	const C_COUNTER = 'COUNTER';
	protected function labels()
	{
		return [
			self::C_ID			 => $this->trans('counter.entity.counter.id'),
			self::C_ENTITY		 => $this->trans('counter.entity.counter.entity'),
			self::C_ENTITY_ID	 => $this->trans('counter.entity.counter.entity_id'),
			self::C_TIMESTAMP_X	 => $this->trans('counter.entity.counter.timestamp_x'),
			self::C_COUNTER		 => $this->trans('counter.entity.counter.counter'),
		];
	}
	protected function rules()
	{
		return[
			[self::C_ENTITY,self::C_ENTITY_ID],
			$this->rule()->string()->setMax(100)->notEmpty(),
			[self::C_TIMESTAMP_X],
			$this->rule()->setter()->setValidators([
				$this->rule()->datetime()->withTime()->notEmpty()
			])->setValue($this->date()->convertTimeStamp()),
			[self::C_COUNTER],
			$this->rule()->number()->integer()->setDefault(1),
		];
	}
}