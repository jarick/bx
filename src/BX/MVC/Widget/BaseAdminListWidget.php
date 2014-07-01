<?php namespace BX\MVC\Widget;
use BX\Error\Error;
use BX\MVC\Widget;
use BX\Validator\IEntity;

abstract class BaseAdminListWidget extends Widget
{
	use \BX\DB\DBTrait;
	const P_QUERY_KEY = 0;
	const P_SESSION_KEY = 1;
	const P_DEFAULT = 2;
	const Q_LIMIT = 'limit';
	const Q_OFFSET = 'offset';
	const Q_SORT = 'sort';
	const Q_FILTER = 'filter';
	const F_JSON = 'json';
	const F_XML = 'xml';
	const F_CSV = 'csv';
	const F_XLS = 'xls';
	const F_P_QUERY_KEY = 0;
	const F_P_MIME_TYPE = 1;
	const F_P_FUNCTION = 2;
	const F_P_FILE_NAME = 3;
	/**
	 * @var array
	 */
	protected $params = [
		self::Q_LIMIT	 => [
			self::P_QUERY_KEY	 => 'limit',
			self::P_SESSION_KEY	 => null,
			self::P_DEFAULT		 => 25,
		],
		self::Q_OFFSET	 => [
			self::P_QUERY_KEY	 => 'page',
			self::P_SESSION_KEY	 => null,
			self::P_DEFAULT		 => 0,
		],
		self::Q_SORT	 => [
			self::P_QUERY_KEY	 => 'sort',
			self::P_SESSION_KEY	 => null,
			self::P_DEFAULT		 => [],
		],
		self::Q_FILTER	 => [
			self::P_QUERY_KEY	 => 'filter',
			self::P_SESSION_KEY	 => null,
			self::P_DEFAULT		 => [],
		]
	];
	/**
	 * @array
	 */
	protected $formats = [
		self::F_JSON => [
			self::F_P_QUERY_KEY	 => 'json',
			self::F_P_MIME_TYPE	 => 'application/json',
			self::F_P_FUNCTION	 => 'actionJson',
			self::F_P_FILE_NAME	 => 'list.json',
		],
		self::F_XML	 => [
			self::F_P_QUERY_KEY	 => 'xml',
			self::F_P_MIME_TYPE	 => 'application/xml',
			self::F_P_FUNCTION	 => 'actionXml',
			self::F_P_FILE_NAME	 => 'list.xml',
		],
		self::F_CSV	 => [
			self::F_P_QUERY_KEY	 => 'csv',
			self::F_P_MIME_TYPE	 => 'application/csv',
			self::F_P_FUNCTION	 => 'actionCsv',
			self::F_P_FILE_NAME	 => 'list.csv',
		],
		self::F_XLS	 => [
			self::F_P_QUERY_KEY	 => 'xls',
			self::F_P_MIME_TYPE	 => 'application/vnd.ms-excel',
			self::F_P_FUNCTION	 => 'actionXls',
			self::F_P_FILE_NAME	 => 'list.xls',
		],
	];
	/**
	 * Constructor
	 */
	protected function init()
	{
		foreach($this->params as $key => &$value){
			if ($value[self::P_SESSION_KEY] === null){
				$value[self::P_SESSION_KEY] = $key.'_'.get_called_class();
			}
		}
	}
	/**
	 * Return limit items in page
	 *
	 * @param string $key
	 * @return integer
	 */
	protected function getQueryParam($key)
	{
		$session = $this->session()->store();
		$value = $this->request()->query()->get($this->params[$key][self::P_QUERY_KEY]);
		$session_key = $this->params[$key][self::P_SESSION_KEY];
		if (is_array($value) || (is_string($value) && $this->string()->length($value) > 0)){
			$session[$session_key] = $value;
		}else{
			if (array_key_exists($session_key,$session)){
				$value = $session[$session_key];
			}else{
				$value = $this->params[$key][self::P_DEFAULT];
			}
		}
		return $value;
	}
	/**
	 * Retrurn sorting array
	 *
	 * @param IEntity $entity
	 * @return array
	 */
	protected function getSorting(IEntity $entity)
	{
		$value = $this->getQueryParam(self::Q_SORT);
		if (is_array($value)){
			foreach($value as $key => &$val){
				if ($entity->exists($key)){
					if ($this->string()->toLower($val) !== 'asc'){
						$val = 'desc';
					}
				}else{
					unset($value[$key]);
				}
			}
		}else{
			$value = [];
		}
		return $value;
	}
	/**
	 * Recursive trim array values
	 *
	 * @param array $value
	 * @return array
	 */
	protected function trim(array $value)
	{
		foreach($value as &$item){
			if (is_array($item)){
				$item = $this->trim($item);
			}else{
				$item = trim($item);
			}
		}
		return $value;
	}
	/**
	 * Render json
	 *
	 * @param \BX\Base\Collection $list
	 */
	public function actionJson($list)
	{
		$array = [];
		foreach($list as $item){
			$array[] = $item->getData();
		}
		print json_encode($array);
	}
	/**
	 * Render xml
	 *
	 * @param \BX\Base\Collection $list
	 */
	public function actionXml($list)
	{
		$xml = new \SimpleXMLElement('<list/>');
		foreach($list as $item){
			$item_xml = $xml->addChild('item');
			foreach($item->getData() as $key => $value){
				$item_xml->addChild($key,$value);
			}
		}
		print $xml->asXML();
	}
	/**
	 * Render xls
	 *
	 * @param \BX\Base\Collection $list
	 */
	public function actionXls($list)
	{
		$this->view->stream(function() use ($list){
			$objPHPExcel = new \PHPExcel();
			$i = 1;
			foreach($list as $item){
				$n = 65;
				foreach($item->getData() as $value){
					$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue(chr($n).$i,$value);
					$n++;
				}
				$i++;
			}
			\PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5')->save('php://output');
		});
	}
	/**
	 * Render csv
	 *
	 * @param \BX\Base\Collection $list
	 */
	public function actionCsv($list)
	{
		$this->view->stream(function() use ($list){
			$objPHPExcel = new \PHPExcel();
			$i = 1;
			foreach($list as $item){
				$n = 65;
				foreach($item->getData() as $value){
					$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue(chr($n).$i,$value);
					$n++;
				}
				$i++;
			}
			\PHPExcel_IOFactory::createWriter($objPHPExcel,'CSV')->save('php://output');
		});
	}
	/**
	 * Delete checked user
	 *
	 * @param \Illuminate\Support\MessageBag $error
	 */
	protected function actionDelete(&$error)
	{
		$action = $this->request()->post()->get('ACTION');
		if ($action !== null){
			if ($action['ACTION'] === 'delete'){
				if (isset($action['ID']) && is_array($action['ID'])){
					$this->transaction()->begin();
					Error::reset();
					foreach($action['ID'] as $id){
						if ($id > 0){
							if (!$this->delete($id)){
								$this->transaction()->rollback();
								$error = new MessageBag(['ID' => Error::get()]);
								return;
							}
						}
					}
					$this->transaction()->commit();
				}
				$this->session()->setFlash($this->getFlashKey(),$this->trans('user.widgets.edit.delete_success'));
				$this->redirect($this->getCurPageParam([],['post']));
			}
		}
	}
	abstract protected function delete($id);
	abstract protected function getFilterForm();
	abstract protected function getFlashKey();
	abstract protected function getTemplate();
	/**
	 * Return SQL Builder
	 *
	 * @param array $sort
	 * @param array $filter
	 * @param type $offset
	 * @param type $limit
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	protected function getList(array $sort,array $filter,$offset = null,$limit = null)
	{
		$return = UserGroup::finder()->sort($sort)->filter($filter);
		if ($offset !== null){
			$return->offset($offset);
		}
		if ($limit !== null){
			$return->limit($limit);
		}
		return $return;
	}
	/**
	 * Render list of users
	 */
	private function actionIndex()
	{
		$filter = $this->getFilterForm();
		$filter_array = $filter->getFilter();
		$error = false;
		$this->actionDelete($error);
		$count = $this->getList(array(),$filter_array)->count();
		$offset = intval($this->getQueryParam(self::Q_OFFSET));
		$limit = intval($this->getQueryParam(self::Q_LIMIT));
		if ($offset >= $limit){
			$offset = 0;
		}
		//TODO
		#$sort = $this->getSorting($filter);
		$sort = [];
		$list = $this->getList($sort,$filter_array,$offset,$limit)->all();
		$format = $this->request()->query()->get('format');
		if ($format !== null){
			foreach($this->formats as $render){
				if ($format === $render[self::F_P_QUERY_KEY]){
					$this->view->buffer()->flush();
					$this->response()->headers['Content-Type'] = $render[self::F_P_MIME_TYPE];
					$content = "attachment; filename=\"{$render[self::F_P_FILE_NAME]}\"";
					$this->response()->headers['Content-Disposition:'] = $content;
					call_user_func([$this,$render[self::F_P_FUNCTION]],$list);
					$this->view->abort();
				}
			}
		}
		$message = $this->session()->getFlash($this->getFlashKey());
		$params = compact('count','list','offset','limit','message','filter','sort','error');
		$this->render($this->getTemplate(),$params);
	}
	/**
	 * Run
	 */
	public function run()
	{
		$this->actionIndex();
	}
}