<?php
/**
 * @Author: Bobby
 * @Date:   2019-01-28 11:34:40
 * @Last Modified by:   Bobby
 * @Last Modified time: 2019-01-28 17:07:06
 */
namespace Bobby\Component\Database;

trait CompilerTrait
{
    private $compileComponent = [
        'column',
        'from',
        'join',
        'where',
        'groupBy',
        'having',
        'orderBy',
        'limit',
        'offset',
        'union'
    ];

    public function getSql()
    {
        $sql = $this->compileSql();
        $bindings = $this->mergeBindings();

        $sql = explode('?', $sql);
        foreach ($sql as $index => $segment) {
            $sql[$index] .= isset($bindings[$index])? "'{$bindings[$index]}'" : '';
        }

        return implode('', $sql);
    }

    public function compileSql()
    {
        $sql = [];
        foreach ($this->compileComponent as $component) {
            if($this->$component !== null) {
                $method = 'compile' . ucfirst($component);
                $sql[] = $this->$method();
            }
        }

        return implode(' ', $sql);
    }

    private function compileFrom()
    {
        return "FROM {$this->from}";
    }

    private function compileColumn()
    {
    	return ($this->distinct ? 'SELECT DISTINCT ' : 'SELECT ') . ($this->column ? implode(',', $this->column) : '*');
    }

    private function compileJoin()
    {
        if (!$this->join) return '';

    	foreach ($this->join as $join) {

    		if (!$join->clauses) $sql[] = "{$join->type} JOIN {$join->prefix}{$join->joinTable}";
            
            $clauses = [];
    		foreach ($join->clauses as $clause) {
    			
    			$clauses[] =  $this->compileJoinConstraint($clause);

    		}

    		$clauses[0] = ltrim($clauses[0], $join->clauses[0]['joint']);

    		$clauses = implode(' ', $clauses);

    		$sql[] = "{$join->type} JOIN {$join->prefix}{$join->joinTable} ON{$clauses}";

    	}

    	return implode(' ', $sql);
    }

    private function compileJoinConstraint($clause)
    {    	

    	if ($clause['type'] === 'nested') {
    		$join = $clause['query'];
    		return $this->compileNestedJoinConstraint($join);
    	} 

    	return "{$clause['joint']} {$clause['left']} {$clause['operator']} {$clause['right']}";
    	
    }

    private function compileNestedJoinConstraint($join)
    {

    	foreach ($join->clauses as $clause) {
    		
    		$clauses[] =  $this->compileJoinConstraint($clause);

    	}

    	return implode(' ', $clauses);

    }

    private function compileWhere()
    {
        if (!$this->where) return '';

        foreach ($this->where as $where) $sql[] = $this->compileWhereConstraint($where);

        $sql[0] = ltrim($sql[0], $this->where[0]['joint']);

        return 'WHERE' . implode(' ', $sql);
    }

    private function compileWhereConstraint(array $where)
    {
        if ($where['type'] === 'basic') return "{$where['joint']} {$where['column']} {$where['operator']} {$where['value']}";

        if ($where['type'] === 'raw') return "{$where['joint']} {$where['sql']}";
    }

    private function compileGroupBy()
    {
        return 'GROUP BY ' . implode(',', $this->groupBy);
    }

    private function compileHaving()
    {
        foreach ($this->having as $having) 
            if ($having['type'] === 'basic') 
                $sql[] =  "{$having['joint']} {$having['column']} {$having['operator']} {$having['value']}";
            else 
                $sql[] = "{$having['joint']} {$having['sql']}";

        
        $sql[0] = ltrim($sql[0], $this->having[0]['joint']);

        return 'HAVING ' . implode(' ', $sql);
    }

    private function compileOrderBy()
    {
        foreach ($this->orderBy as $orderBy) 
            if ($orderBy['type'] === 'basic')
                $sql[] = "{$orderBy['column']} {$orderBy['sort']}";    
            else
                $sql[] = "{$orderBy['sql']}";

        return 'ORDER BY ' . implode(',', $sql);
    }

    private function compileOffset()
    {
        return 'OFFSET ' . $this->offset;
    }

    private function compileLimit()
    {
        return 'LIMIT ' . $this->limit;
    }

    private function compileUnion()
    {
        foreach ($this->union as $union) {
            $sql[] = ($union['distinct'] ? 'UNION DISTINCT ' : 'UNION ALL ') . $union['sql'];
        }

        return implode(' ', $sql);
    }

    private function compileInsert(array $values)
    {
        if (!current($values)) {
            $columns = array_keys($values);
            $bindings = array_values($values);
            $insert = ' (' . implode(',', array_fill(0, count($values), '?')) . ')';
        } else {
            $columns = array_keys($values[0]);
            $insert = '(' . implode(',', array_fill(0, count($values[0]), '?')) . ')';
            $insert = implode(' ', array_fill(0, count($values), $insert));
            $bindings = [];

            foreach ($values as $key => $value) {
                if (is_array($value)) {
                    $bindings[] = array_merge($bindings, array_values($value));
                }  
            }
        }

        return [
            'INSERT INTO ' . $this->from . ' (' . implode(',', $columns) . ') VALUES' . $insert,
            $bindings
         ];
    }

    private function compileUpdate(array $values)
    {
        $columns = array_keys($values);
        $bindings = array_values($values);
        
        return [
            'UPDATE ' . $this->from . ' ' . $this->compileJoin() . ' SET ' . implode('=?,', $columns) . '=? ' . $this->compileWhere(),
            array_merge($this->getBindings('join'), $bindings, $this->getBindings('where'))
        ];
    }

    private function compileIncrement(array $values)
    {
        $updateSet = '';

        foreach ($values as $key => $value) $updateSet .= "$key = $key + $value,";

        $updateSet = rtrim($updateSet, ',');

        return [
            'UPDATE ' . $this->from . ' ' . $this->compileJoin() . ' SET ' . $updateSet . ' ' . $this->compileWhere(),
            array_merge($this->getBindings('join'), $this->getBindings('where'))
        ];
    }

    private function compileDecrement(array $values)
    {
        $updateSet = '';

        foreach ($values as $key => $value) $updateSet .= "$key = $key - $value,";

        $updateSet = rtrim($updateSet, ',');

        return [
            'UPDATE ' . $this->from . ' ' . $this->compileJoin() . ' SET ' . $updateSet . ' ' . $this->compileWhere(),
            array_merge($this->getBindings('join'), $this->getBindings('where'))
        ];
    }

    private function compileDelete()
    {
        return [
            'DELETE FROM ' . $this->from . ' ' . $this->compileJoin() . ' ' . $this->compileWhere(),
            array_merge($this->getBindings('join'), $this->getBindings('where'))
        ];
    }

}