# Miao Router
## Вступление
Генерация "правильных" uri может оказаться нетревиальный задачей при разработке большого 
приложения несколькими программистами, также необходимо соблюдать требования SEO. Одним из решений является 
создания описания правил маршрутов для uri. Ознакомится с примерами маршрутизаторов можно на 
фреймворках Zend Framework, yii.

В отличие от остальных приложений, предполагается использование перенаправлений на стороне web-servera (nginx, apache), 
это позволяет в разы увеличить скорость обработки правил, в отличие от разрбора запроса на стороне php. На стороне 
сервера модуль используется для генерации "правильных" uri.

## Пример

Конфигурационный файл свойств в автосборке haru.

*build/properties/parts/daily_routes.xml*

```xml
<?xml version="1.0" encoding="UTF-8"?>
<config>
  <libs>
	<Daily>
		<modules>
			<BackOffice>
				<Router>
					<main>Main</main>
					<error>404</error>
					<defaultPrefix>Daily_BackOffice</defaultPrefix>
					<route>
						<rule>/news/list/:id</rule>
						<view>News_List</view>
						<validator param="id" type="Numeric" min="0" />
					</route>
					<route>
						<rule>/publisher/list/:id</rule>
						<view>Publisher_List</view>
						<validator param="id" type="Numeric" min="0" />
					</route>
					<route>
						<rule>/article/edit/main/:id</rule>
						<view>Article_EditMain</view>
						<validator param="id" type="Numeric" min="5" />
					</route>
            ...
</config>
```

Создайте класс расширение для вашей библиотеки (Daily)
```php
class Daily_BackOffice_Router extends Miao_Router
{

  static public function getInstance()
	{
		$index = __CLASS__;
		if ( Miao_Registry::isRegistered( $index ) )
		{
			$result = Miao_Registry::get( $index );
		}
		else
		{
			$config = Miao_Config::Libs( __CLASS__ );
			$result = self::factory( $config->toArray() );
			Miao_Registry::set( $index, $result );
		}
		return $result;
	}
}
```

Используйте для создания uri
```php
$router = Daily_BackOffice_Router::getInstance();
$uri = $router->view( 'Article_EditMain', array( 'id' => 123 ) );
```

## Основные функции
* генерация ссылок
* преобразование uri в параметры для Miao_Office
* валидация параметров
* генерация правил для .htaccess
* генерация правил для .nginx