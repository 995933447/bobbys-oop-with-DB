<?php
namespace Bobby\Component\Database;

use Bobby\{
	Contract\Database\SqlBuilder as SqlBuilderContract,
	Component\Database\Builder\JoinSqlBuilder
};

class SqlBuilder implements SqlBuilderContract
{
	use CompilerTrait;

	protected $currentInstance;

	protected $useConnector;

	protected $prefix = '';

	protected $distinct = false;

	protected $column = '';

	protected $from;

	protected $join;

	protected $where;

	protected $groupBy;

	protected $having;

	protected $orderBy;

	protected $limit;

	protected $offset;

	protected $union;

	protected $lock;

	protected $bindings = [
		'select' => [],
		'from' => [],
		'join' => [],
		'where' => [],
		'having' => [],
		'order' => [],
		'union' => []
	];

	public function setConnectInstance($connectInstance, $useConnector = null, $prefix)
	{
		$this->currentInstance = $connectInstance;
		$this->useConnector = $useConnector;
		$this->prefix = $prefix;

		return $this;
	}

	public function table($table, $prefix = null)
	{
		if (!is_string($table)) {
			list($table, $bindings) = $this->parseSubQuery($table);
			if ($bindings) $this->bindings['from'] = array_merge($this->bindings['from'], $bindings);
			$this->from = $table;
		} else {
			$this->from = (is_null($prefix) ? $this->prefix : $prefix) . $table;
		}

		return $this;
	}

	public function column(array $column)
	{
		$this->column = $this->column ? array_merge($this->column, $column) : $column;
		return $this;
	}

	public function columnRaw(string $sql, array $bindings = null)
	{
		if ($this->column)
			$this->column = [$sql];
		else
			$this->column[] = $sql;

		$this->bindings['select'] = $bindings ? array_merge($this->bindings['select'], $bindings) : $this->bindings['select'];

		return $this;
	}

	public function columnSub($query)
	{
		list($column, $bindings) = $this->parseSubQuery($query);
		$this->columnRaw($column, $bindings);
		return $this;
	}

	protected function newSqlBuilder($connectInstance = null, $connector = null, $prefix = null)
	{
		return (new Static)->setConnectInstance($connectInstance?: $this->currentInstance, $connector?: $this->useConnector, $prefix?: $this->prefix);
	}

	protected function parseSubQuery($query)
	{
		if ($query instanceof \Closure) {
			$callback = $query;
			$callback($query = $this->newSqlBuilder());
		} else if(!$query instanceof SqlBuilderContract) {
			throw new InvalidArgumentException('The argument of ' . __CLASS__ . '::' . __FUNCTION__ . 'must be closure or Database instance');
		}

		return ['(' . $query->compileSql() . ')', $query->getBindings()];
	}

	protected function getBindings($type = null)
	{
		return $type ? $this->bindings[$type] : $this->bindings;
	}

	public function distinct()
	{
		$this->distinct = true;
		return $this;
	}

	public function join($table, $conditionLeft = null, $operator = null, $conditionRight = null, $type = 'INNER')
	{
		$join = (new JoinSqlBuilder)->setJoinType($this->prefix, $type, $table);

		if($conditionLeft instanceof \Closure)
			$conditionLeft($join);
		else
			$join->on($conditionLeft, $operator, $conditionRight);


		$this->join[] = $join;
		$this->bindings['join'] = array_merge($this->bindings['join'], $join->getBindings());

		return $this;
	}

	public function where($column, $operator = null, $value = null, $joint = 'AND')
	{

		if (is_array($column)) return $this->whereArray($column, __FUNCTION__);

		if ($column instanceof \Closure) return $this->whereNested($column);

		if (func_num_args() === 2) list($operator, $value) = ['=', $operator];

		if ($value instanceof \Closure || $value instanceof SqlBuilderContrac) return $this->whereSub($column, $operator, $value, $joint);

		if (is_array($value)) {
			$this->bindings['where'] = array_merge($this->bindings['where'], $value);
			$value = '(' . implode(',', array_fill(0, count($value), '?')) . ')';
		} else {
			$this->bindings['where'][] = $value;
			$value = '?';
		}

		$type = 'basic';
		$this->where[] = compact('type', 'column', 'operator', 'value', 'joint');

		return $this;
	}

	public function whereArray(array $wheres, $method)
	{
		foreach ($wheres as $where) $this->$method(...array_values($where));
		return $this;
	}

	public function whereNested(\Closure $where)
	{
		$where($this);
		return $this;
	}

	public function whereSub($column, $operator, $value, $joint = 'AND')
	{
		$query = $this->parseSubQuery($value);

		$this->where[] = ['type' => 'basic', 'column' => $column, 'operator' => $operator, 'value' => $query[0], 'joint' => $joint];

		foreach ($query[1] as $binding) 
			if ($binding)
				$this->bindings['where'] = array_merge($this->bindings['where'], $binding);

		return $this;
	}

	public function whereRaw($sql, array $bindings = null, $joint = 'AND')
	{
		$this->where[] = [
			'type' => 'raw',
			'sql' => "($sql)",
			'joint' => $joint
		];

		if ($bindings) $this->bindings['where'] = array_merge($this->bindings['where'], $bindings);

		return $this;
	}

	public function groupBy(array $column)
	{
		$this->groupBy = $this->groupBy ? array_merge($this->groupBy, $column) : $column;
		return $this;
	}

	public function having($column, $operator, $value, $joint = 'AND')
	{
		if (is_array($column)) return $this->whereArray($column, __FUNCTION__);

		if (func_num_args() === 2) list($operator, $value) = ['=', $operator];

		if (is_array($value)) {
			$this->bindings['having'] = array_merge($this->bindings['having'], $value);
			$value = '(' . implode(',', array_fill(0, count($value), '?')) . ')';
		} else {
			$this->bindings['having'][] = $value;
			$value = '?';
		}

		$type = 'basic';
		$this->having[] = compact('type', 'column', 'operator', 'value', 'joint');

		return $this;
	}

	public function havingRaw($sql, array $bindings = null)
	{
		$this->having[] = [
			'type' => 'raw',
			'sql' => "($sql)",
			'joint' => $joint
		];

		if ($buildings) $this->buildings['having'] = array_merge($this->buildings['having'], $buildings);

		return $this;
	}

	public function orderBy($column, $sort = 'ASC')
	{
		$type = 'basic';
		$this->orderBy[] = compact('type', 'column', 'sort');
		return $this;
	}

	public function orderByRaw($sql, array $bindings = null)
	{
		$type = 'raw';
		$this->orderBy[] = compact('type', 'sql', 'bindings');
		if($bindings) $this->bindings['order'] = array_merge($this->bindings['order'], $bindings);
	}

	public function offset($value)
	{
		$this->offset = $value;
		return $this;
	}

	public function limit($value)
	{
		$this->limit = $value;
		return $this;
	}

	public function union($query, $distinct = true)
	{
		$query = $this->parseSubQuery($query);
		$this->union[] = ['sql' => $query[0], 'distinct' => $distinct];
		foreach ($query[1] as $binding) 
			if ($binding)
				$this->bindings['union'] = array_merge($this->bindings['union'], $binding);

		return $this;
	}

	public function select()
	{
		$bindings = $this->mergeBindings();
		return $this->currentInstance->query($this->compileSql(), $bindings, $this->useConnector);
	}

	private function mergeBindings()
	{
		$bindings = [];
		foreach ($this->getBindings() as $binding) 
			$bindings = $binding ? array_merge($bindings, $binding) : $bindings;
		return $bindings;
	}

	public function find()
	{
		$originalLimit = $this->limit;
		$this->limit = 1;
		$result = $this->select();
		$this->limit = $originalLimit;
		return $result? $result[0] : $result;
	}

	protected function aggregate($aggregate, $column)
	{
		$originalColumn = $column;
		$this->column = ["{$aggregate}({$column})"];
		$result = (int)$this->select();
		$this->column = $column;
		return $result;
	}

	public function max($column = '*')
	{
		return $this->aggregate('max', $column);
	}

	public function min($column = '*')
	{
		return $this->aggregate('min', $column);
	}

	public function sum($column = '*')
	{
		return $this->aggregate('sum', $column);
	}

	public function count($column = '*')
	{
		return $this->aggregate('count', $column);
	}

	public function average($column = '*')
	{
		return $this->aggregate('average', $column);
	}

	public function insert($value)
	{
		if (is_string($value)) 
			$arguments = func_get_args();
		else 
			$arguments = $this->compileInsert($value);

		return $this->currentInstance->insert(...$arguments);
	}

	public function update($value)
	{
		if (is_string($value))
			$arguments = func_get_args();
		else
			$arguments = $this->compileUpdate($value);

		return $this->currentInstance->update(...$arguments);
	}

	private function incOrDec($method, array $values)
	{
		$method = 'compile' . ucfirst($method);
		$arguments = $this->{$method}($values);
		return $this->update(...$arguments);
	}

	public function increment(array $values)
	{
		return $this->incOrDec(__FUNCTION__, $values);
	}

	public function decrement(array $values)
	{
		return $this->incOrDec(__FUNCTION__, $values);
	}

	public function delete(string $sql = null, array $bindings = [])
	{
		if (!$sql) 
			list($sql, $bindings) = $this->compileDelete();
		return $this->currentInstance->delete($sql, $bindings);
	}

}