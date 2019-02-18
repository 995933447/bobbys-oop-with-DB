<?php 
namespace Bobby\Contract\Database;

interface Instance
{
	public function connectRead();

	public function connectWrite();

	public function table($name, $prefix = null);

	public function query($sql, array $parameters = null, $connector = null);

	public function insert($sql, array $parameters = null, $connector = null);

	public function update($sql, array $parameters = null, $connector = null);

	public function delete($sql, array $parameters = null, $connector = null);

	public function initSqlBuidler($connector = null, $prefix);
}