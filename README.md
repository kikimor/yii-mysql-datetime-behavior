# Yii MySQLDateTimeBehavior

## What is it?
Automatically converts date and datetime fields between ActiveRecord model and MySQL database for Yii framework.

## How to instal?
```
composer require kikimor/yii-mysql-datetime-behavior dev-master
```
## How to configure?
```php
<?php
use Kikimor\MySQLDateTimeBehavior\MySQLDateTimeBehavior;
...
class model extends \CActiveRecord
{
  public function behaviors()
  {
  	return array_merge(parent::behaviors(), [
  		'DateTimeBehavior' => [
  		  'class' => MySQLDateTimeBehavior::class, 
  		  'dateFormat' => 'd.m.Y', // by default
  		  'dateTimeFormat' => 'd.m.Y H:i', // by default
  		],
  	]);
  }
}
```

## How to use?
```php
$model = Model::model()->findByPk(1);
echo $model->date; // 25.02.2015
$model->date = '26.02.2015';
$model->save();
```
