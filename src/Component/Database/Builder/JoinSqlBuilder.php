<?php
namespace Bobby\Component\Database\Builder;

use Bobby\Component\Database\SqlBuilder;

class JoinSqlBuilder extends SqlBuilder
{
	protected $clauses;

    protected $type;

    protected $joinTable;

    protected $bindings = [];

	public function setJoinType($prefix, $type, $table)
	{
		$this->prefix = $prefix;
		$this->type = $type;
		$this->joinTable = $table;
        return $this;
	}

	public function on($conditionLeft, $operator, $conditionRight, $joint = 'AND', $where = false)
	{

        if($conditionLeft instanceof \Closure) {

            $conditionLeft($join = new Static);
            
            if($join->clauses) {
            	$this->clauses[] = ['type' => 'nested', 'query' => $join];
            	$this->bindings = array_merge($this->bindings, $join->bindings);
            }

        } else {

        	if($where) {

        		if(is_array($conditionRight)) {
        			$this->bindings = array_merge($this->bindings, $conditionRight);
        			$conditionRight = '(' . implode(',', array_fill(0, count($conditionRight), '?')) . ')';
        		} else {
        			$this->bindings[] = $conditionRight;
        			$conditionRight = '?';
        		}

        	}

        	if($conditionLeft) {
        		$this->clauses[] = ['type' => 'basic', 'left' => strpos($conditionLeft, '.') === false? $conditionLeft : $this->prefix . $conditionLeft, 'operator' => $operator, 'right' => strpos($conditionRight, '.') === false? $conditionRight : $this->prefix . $conditionRight, 'joint' => $joint];
        	}

        	return $this;

        }

	}

	public function and($conditionLeft, $operator, $conditionRight, $joint = 'and')
	{
		return $this->on($conditionLeft, $operator, $conditionRight, $joint, true);
	}

}