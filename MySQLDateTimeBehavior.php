<?php
namespace Kikimor\MySQLDateTimeBehavior;

use CActiveRecordBehavior;
use CDbExpression;

/**
 * Class MySQLDateTimeBehavior
 * Automatically converts date and datetime fields between ActiveRecord model and MySQL database.
 *
 * Author: kikimor <i@kikimor.ru>
 * Version: 1.1.2
 * Requires: Yii 1.0.9 version
 */
class MySQLDateTimeBehavior extends CActiveRecordBehavior
{
	/**
	 * Date format (PHP date format)
	 * @var string
	 */
	public $dateFormat = 'd.m.Y';
	/**
	 * DateTime format (PHP date format)
	 * @var string
	 */
	public $dateTimeFormat = 'd.m.Y H:i';
	/*
	 * Source columns data.
	 */
	private $sourceValues;

	/**
	 * @inheritdoc
	 */
	public function afterFind($event)
	{
		foreach ($event->sender->tableSchema->columns as $columnName => $column) {
			if ($column->dbType != 'date' and $column->dbType != 'datetime') continue;

			if (($timestamp = strtotime($event->sender->$columnName)) !== false) {
				switch ($column->dbType) {
					case 'date':
						$event->sender->$columnName = date($this->dateFormat, $timestamp);
						break;
					case 'datetime':
						$event->sender->$columnName = date($this->dateTimeFormat, $timestamp);
						break;
				}
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function beforeSave($event)
	{
		$this->sourceValues = [];
		foreach ($event->sender->tableSchema->columns as $columnName => $column) {
			if ($column->dbType != 'date' and $column->dbType != 'datetime') continue;
			$this->sourceValues[$columnName] = $event->sender->$columnName;

			if (($timestamp = strtotime($event->sender->$columnName)) !== false) {
				$key = ':date' . md5($columnName);
				$event->sender->$columnName = new CDbExpression(
					'STR_TO_DATE(' . $key . ', "%d%m%Y%H%i%s")',
					[$key => date('dmYHis', $timestamp)]
				);
			} elseif ($column->allowNull && !is_object($event->sender->$columnName)) {
				$event->sender->$columnName = new CDbExpression('NULL');
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function afterSave($event)
	{
		foreach ($event->sender->tableSchema->columns as $columnName => $column) {
			if ($column->dbType != 'date' and $column->dbType != 'datetime') continue;
			$event->sender->$columnName = $this->sourceValues[$columnName];
		}
	}
}
