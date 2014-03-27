<?php namespace BX\Console\Widget;
use BX\MVC\Widget;
use BX\Migration\Command\Migrate;
use \BX\Validator\Manager\String;

class Console extends Widget
{
	use \BX\Console\ConsoleTrait,
	 \BX\Validator\ValidatorTrait;
	CONST EVENT_LIST_COMMAND = 'console.widget.console.list_command';
	protected function controller()
	{
		$controller = $this->console();
		$this->fire(self::EVENT_LIST_COMMAND,[$controller->command]);
		$controller->command->attach(new Migrate());
		return $controller;
	}
	protected function getDefault()
	{
		return [
			'CODE' => null,
		];
	}
	protected function rules()
	{
		return [
#			['SESSID', ''],
			['CODE',String::create()->notEmpty()->setMax(1024 * 1024)],
		];
	}
	protected function labels()
	{
		return [
			'CODE' => $this->trans('console.widgets.console.label_code'),
		];
	}
	public function run()
	{
		$post = $this->request()->post()->get('FORM');
		$validator = $this->validator($this->rules(),$this->labels());
		if ($post !== null){
			$this->view()->buffer()->flush();
			if ($validator->check($post)){
				$this->controller()->exec($post['CODE']);
			} else{
				foreach($validator->getErrors()->all() as $message){ 
					$this->view()->widget('message',['message' => $message,'type' => 'danger']);
				}
			}
			$this->view()->abort();
		} else{
			$post = $this->getDefault();
		}
		$this->render(false,[
			'validator'	 => $validator,
			'post'		 => $post,
		]);
	}
}