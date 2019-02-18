<?php 
namespace Bobby\Contract\Database;

interface SqlBuilder
{

	public function setConnectInstance($connectInstance, $useConnector = null, $prefix);

	public function table($table, $prefix = null);

	public function column(array $column);

	public function columnRaw(string $sql, array $bindings = null);

	public function columnSub($query);

	public function distinct();

	public function join($table, $conditionLeft = null, $operator = null, $conditionRight = null, $type = 'INNER');

	public function where($column, $operator = null, $value = null, $joint = 'AND');

	public function whereArray(array $wheres, $method);

	public function whereNested(\Closure $where);

	public function whereSub($column, $operator, $value, $joint = 'AND');

	public function whereRaw($sql, array $bindings = null, $joint = 'AND');

	public function groupBy(array $column);

	public function having($column, $operator, $value, $joint = 'AND');

	public function havingRaw($sql, array $bindings = null);

	public function orderBy($column, $sort = 'ASC');

	public function orderByRaw($sql, array $bindings = null);

	public function offset($value);

	public function limit($value);

	public function union($query, $distinct = true);

	public function select();

	public function find();

	public function max($column = '*');

	public function min($column = '*');

	public function sum($column = '*');

	public function count($column = '*');

	public function average($column = '*');

	public function insert($value);

	public function update($value);

	public function increment(array $values);

	public function decrement(array $values);

	public function delete(string $sql = null, array $bindings = []);

}