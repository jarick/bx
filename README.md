# BX framework

## Общая архитектура.

##### Для работы необходим php версии 5.4 либо более новая версия, memcache(для хранения кеша в случае отсуствия сайт не будет работать с кешем), mysql или sqlite. 

##### Стандарт кодирования.

Framework полностью построен на стандартнах [psr](https://github.com/php-fig/fig-standards/blob/master/accepted/). За исключением правила использования табуляции(используются табы вместо пробелов).

##### Структура.

Код располагается в папке **src** и разбит на независимые компоненты. Структура компонента, разбивается на 4 бизнес слоя:

* Модель(*Entity*) простой класс наследуется от интерфейса **IEntity** и использует трейт **EntityTrait**.

 В функции **labels** перечисляются название полей. Пример:

```php
	protected function labels()
	{
		return [
			self::C_ID		 => $this->trans('user.entity.user.id'),
			self::C_UNIQUE_ID	 => $this->trans('user.entity.user.unique_id'),
			self::C_LOGIN		 => $this->trans('user.entity.user.login'),
			self::C_PASSWORD	 => $this->trans('user.entity.user.password'),
			self::C_EMAIL		 => $this->trans('user.entity.user.email'),
			self::C_CODE		 => $this->trans('user.entity.user.code'),
			self::C_CREATE_DATE	 => $this->trans('user.entity.user.create_date'),
			self::C_TIMESTAMP_X	 => $this->trans('user.entity.user.timestamp_x'),
			self::C_REGISTERED	 => $this->trans('user.entity.user.registered'),
			self::C_ACTIVE		 => $this->trans('user.entity.user.active'),
			self::C_DISPLAY_NAME => $this->trans('user.entity.user.display_name'),
		];
	}
```
В функции **rules** перечисляются правила валидации полей. Пример:

```php	
	protected function rules()
	{
		return [
			[self::C_LOGIN,self::C_EMAIL],
			$this->rule()->string()->notEmpty()->setMax(50),
			[self::C_PASSWORD],
			$this->rule()->custom([$this,'filterPassword'])->notEmpty(),
			[self::C_CODE],
			$this->rule()->setter()->setFunction([$this,'filterCode'])->setValidators([
				$this->rule()->string()->notEmpty()->setMax(50),
			]),
			[self::C_CREATE_DATE],
			$this->rule()->setter()->setValidators([
				$this->rule()->datetime()->withTime()->notEmpty()
			])->setValue($this->date()->convertTimeStamp())->onAdd(),
			[self::C_TIMESTAMP_X],
			$this->rule()->setter()->setValidators([
				$this->rule()->datetime()->withTime()->notEmpty()
			])->setValue($this->date()->convertTimeStamp())->onAdd(),
			[self::C_DISPLAY_NAME],
			$this->rule()->string()->setMax(100),
			[self::C_REGISTERED,self::C_ACTIVE],
			$this->rule()->boolean(),
		];
	}
```
Перечисление правил идет по порядку. Сначала задается набор полей, затем правило валидации для этих полей. Для одного поля можно задать любое количество правил. При валидации поля сначала применяются более ранние элементы  из массива, если валидатор выдал ошибку, то следующие валидаторы для поля не будут применены.

Свои фильтры и валидаторы для полей можно задать с помощью функции **$this->rule()->custom**. Пример:
```php	
	public function filterPassword(&$value)
	{
		if ($this->string()->length($value) === 0){
			return $this->trans('user.entity.user.error_password_empty');
		}
		$min = $this->getMinLengthPassword();
		if ($this->string()->length($value) < $min){
			return $this->trans('user.entity.user.error_password_min',['#MIN#' => $min]);
		}
		$value = password_hash($value,PASSWORD_BCRYPT);
	}
	public function filterCode(&$value)
	{
		$value = $this->string()->substr($this->string()->getSlug($this->getValue(self::C_LOGIN)),0,50);
	}
```
Как видно из примера, для того чтобы поменять значение введеного значения поля нужно изменить переменную **$value**. Чтобы сгенерировать ошибку нужно вернуть строку с ошибкой. Если валидация прошла успешно, то можно ничего не возвращать или вернуть null.

**Entity** инкапсулирует в себе значение полей, названия полей, валидаторы и ошибки валидации. Чтобы задать значение для сущности нужно передать массив со значениями в функцию setData().

Чтобы свалидировать переданные значения нужно выполнить функцию **checkFields**. Для получения ошибок валидации нужно вызвать функцию **getErrors**.

* Слой хранения данных(*Store*). В этом слою описана логика работы со сущностями(Entity) для различных баз данных. К пример сессию пользователя мы можем хранить в сессии PHP, в базе данных, или в noSql хранилищах. В этом случае бизнесс логике не обязательно знать какое-именно хранилище использовать. Она будет работать с единным интерфейсом для всех хралищ и с одной и той же сущностью.

Задание способа хранения данных идет в классе *BX\Base\Registry*. Если механизм хранения не указан, framework сам выбирает оптимальный метод хранения. 

Пример конфигурации с реального проекта:
```yaml
	sites:
	    ###:
	        title: '###'
	        keywords: '###'
	        charset: UTF-8
	        name: ###
	        regex:
	            - ###.com
	            - localhost
	        folder: /
	        layout_rule:
	            ###:
	                - ""
	            admin:
	                - admin\/
	        url_rewrite:
	            console: /console/
	            news: /news/
	            admin: /admin/
	lang: ru
	date:
	    timezone: Europe/Kaliningrad
	mode: production
	templating:
	    engine: haml
	    haml: ~/../haml
	    php: ~/../cache
	    doc_root: ~/../www
	cache:
	    type: memcache
	    host: localhost
	    post: 11211
	pdo:
	    dsn: 'sqlite:../db.db'
	zend_search:
	    morphy_dicts: ~/../search/dicts
	    stop_words: ~/../search/stop-words/stop-words-ru.txt
	    index: ~/../search/data
	user:
	    password_min_length: 6
```
Конфигурацию можно указывать в формате php array или yaml, см. второй параметр функции **Registry::init**. 

* Слой управления данными(*Manager*). Главный слой в которым находится вся логика по работе с данными. Как правило вызов менеджеров спрятан в трейты, которые хранят экземпляры объектов в ioc хранилище(см. класс *BX\Base\DI*). В своей логике всегда можно переопределить стандартные стандартные классы менеджеров на свой класс. Пример:
```php
	function it_clearCache(CacheManager $cache)
	{
		$cache->clearByTags('test')->shouldBeCalled()->willReturn(null);
		DI::set('cache',$cache->getWrappedObject());
		$this->clearCache();
		DI::set('cache',null);
	}
```
В данном примере мы переопределили стандартный менеджер на свой Mock класс, чтобы проверить корректную работу функции **clearCache**. Затем мы очистили контейнер, чтобы framework дальше работал с менеджером кеша по умолчанию.

* Слой представления(Widget). Класс наследуется от класса *BX\MVC\Widget*. Должен содержать в себе как можно меньше бизнесс-логики. Используется как прокси-класс, чтобы получить Request параметры и передать их слою управления данными(Manager). Пример:
```php
	class CaptchaWidget extends Widget
	{
		use \BX\Translate\TranslateTrait;
		public function run()
		{
			$this->view->buffer()->flush();
			try{
				$ip = $this->request()->server()->get('REMOTE_ADDR');
				$cpt = new CaptchaManager($ip);
				if ($this->request()->query()->has('reload')){
					$cpt->reload();
				}
				$builder = new CaptchaRender($cpt->getEntity()->code);
				$this->response()->headers['Content-type'] = 'image/jpeg';
				$builder->output();
			}catch (\Exception $e){
				$this->log('captcha.widget.captcha')->err($e);
				echo $this->trans('captcha.widget.captcha.error_generate_captcha');
			}
			$this->view->abort();
		}
	}
```
##### Выборка значений из базы.
ORM фреймворка написан с помощью паттерна UnitOfWork и гарантирует защиту от [Race condition](http://ru.wikipedia.org/wiki/%D0%A1%D0%BE%D1%81%D1%82%D0%BE%D1%8F%D0%BD%D0%B8%D0%B5_%D0%B3%D0%BE%D0%BD%D0%BA%D0%B8). Пример изменения данных:
```php
	/**
	 * Clear old captcha
	 * @param integer $day
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function clear($day = 30)
	{
		$repository = new Repository('captcha');
		$time = $this->date()->convertTimeStamp(time() - $day * 3600 * 24);
		$captches = static::finder(CaptchaEntity::getClass())
			->filter(['<TIMESTAMP_X' => $time])
			->all();
		foreach($captches as $captcha){
			$repository->delete($this,$captcha);
		}
		if (!$repository->commit()){
			$mess = print_r($repository->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException('Error clear old captches. Error:'.$mess);
		}
		return true;
	}
```
В данном примере мы удаляем из базы все старые записи хешей капчи. Чтобы начать изменения нужно создать экзепляр класса  Repository. Добавление идет с помощью функции **add**, изменение с помощью функции **update** и удаление с помощью функции **delete**. Чтобы измения вступили в силу нужно вызвать функцию **commit**. Которая в случаи ошибки вернет false.

Чтобы осуществить фильтрацию нужно вызвать функцию **BX\DB\TableTrait::finder**(см. скласс *BX\DB\Filter\SqlBuilder*).

##### Пример индексного файла.
```php
	<?php
	require dirname(__DIR__).'/vendor/autoload.php';
	BX\Base\Registry::init(dirname(__DIR__).'/config/main.yml');
	BX\MVC\SiteController::run()->end();
```