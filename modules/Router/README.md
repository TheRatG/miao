# Miao Router
## Вступление
Генерация "правильных" uri может оказаться нетревиальный задачей при разработке большого 
приложения несколькими программистами, также необходимо соблюдать требования SEO. Одним из решений является 
создания описания правил маршрутов для uri. Ознакомится с примерами маршрутизаторов можно на 
фреймворках Zend Framework, yii.

В отличие от остальных приложений, предполагается использование перенаправлений на стороне web-servera (nginx, apache), 
это позволяет в разы увеличить скорость обработки правил, в отличие от разрбора запроса на стороне php. На стороне 
сервера модуль используется для генерации "правильных" uri.

## Основные функции
* генерация ссылок
* преобразование uri в параметры для Miao_Office
* валидация параметров
* генерация правил для .htaccess
* генерация правил для .nginx
* получение текущих route-параметров

## Use case
![Use case](http://theratg.github.io/miao/images/route_usecase.png)

## Configurations

## Validators

Элементом для валидатора считается текст после символа "/". */articles/commerce/id.html* будет иметь три валидатора.
Валидатором по умолчанию является *NotEmpty*.
Для того чтобы обозначить что секция является динамической, после слеша */* ставим двоеточие *:*. Имя секции может состоять только из латинских букв.
```
/:preview/:sectionName/:id
``` 
Здесь определено три параметра. Далее по имени параметра привязываем валидаторы.

### In

Определяет возможные значения параметра.

Параметры

* variants - варианты параметра
* delimiter - разделитель. *|* - по умолчанию

```xml
<validator param="nick" 
    type="In"
    variants="politics|economy"                                
    delimiter="|" />
```                                

### Numeric
Часть uri может быть только числой

Параметры

* min - минимальное значение
* max - максимальное значение

```xml
<validator
    param="id"
    type="Numeric"
    min="0" />
```

### Regexp
Проверка при помощи регулярного выражения

Параметры

* pattern - регулярное выражение
* slash - количество захваченных слешей

```xml
<validator
    param="page"
    type="Regexp"
    pattern="p([0-9]+)" />

<!-- если в регулярки слеши, то обязательно обозначаем их количество -->
<validator
    param="date"
    type="regexp"
    slash="2"
    pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}" />
```

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

Поместите скрипт генератор в раширение автосборки
```xml
<?xml version="1.0" encoding="UTF-8"?>
<project
    name="Project Configure"
    default="main">
    <target name="main">            
        <exec
            command="${system.bin.php} ${libs.Daily.deploy.dst}/build/scripts/rewrites.php"
            passthru="true"
            checkreturn="true"
            level="info" />
            ...
```
