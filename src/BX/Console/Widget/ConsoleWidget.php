<?php namespace BX\Console\Widget;
use BX\MVC\Widget;
use BX\Migration\Command\Migrate;
use BX\Validator\Collection\String;
use BX\MVC\Widget\MessageWidget;

class ConsoleWidget extends Widget
{
	use \BX\Console\ConsoleTrait,
	 \BX\Validator\ValidatorTrait,
	 \BX\Translate\TranslateTrait;
	const C_CODE = 'CODE';
	const EVENT_LIST_COMMAND = 'console.widget.console.list_command';
	protected function controller()
	{
		$controller = $this->console();
		$this->fire(self::EVENT_LIST_COMMAND,[$controller->command]);
		$controller->command->add(new Migrate());
		return $controller;
	}
	protected function getDefault()
	{
		return [
			self::C_CODE => null,
		];
	}
	protected function rules()
	{
		return [
			[
				[self::C_CODE],
				String::create()->notEmpty()->setMax(1024 * 1024)
			],
		];
	}
	protected function labels()
	{
		return [
			self::C_CODE => $this->trans('console.widgets.console.label_code'),
		];
	}
	public function run()
	{
		$post = $this->request()->post()->get('FORM');
		if ($post !== null){
			$this->view()->buffer()->flush();
			$validator = $this->validator($this->rules(),$this->labels());
			$post = array_map('trim',$post);
			if ($validator->check($post)){
				$this->controller()->exec($post['CODE']);
			}else{
				foreach($validator->getErrors()->all() as $message){
					$params = ['message' => $message,'type' => 'danger'];
					MessageWidget::widget($this->view(),$params);
				}
			}
			$this->view()->abort();
		}else{
			$post = $this->getDefault();
		}
		$this->render('console/console',[
			'post' => $post,
		]);
	}
}