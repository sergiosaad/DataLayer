<?php

namespace SergioSaad\DataLayer;

use PDO;
use stdClass;
use Exception;
use PDOException;

/**
 * Class DataLayer
 * @package SergioSaad\DataLayer
 */
abstract class DataLayer
{
    use CrudTrait;

    /** @var string $entity database table */
    private $entity;

    /** @var string $primary table primary key field */
    private $primary;

    /** @var array $required table required fields */
    private $required;

    /** @var string $timestamps control created and updated at */
    private $timestamps;

	
    /** @var string */
    protected $statement;
	
    /** @var string */
    protected $params;
	
    /** @var string */
    protected $group;
	
    /** @var string */
    protected $order;
	
    /** @var int */
    protected $limit;
	
    /** @var int */
    protected $offset;
	
    /** @var \PDOException|null */
    protected $fail;
	
    /** @var object|null */
    protected $data;

    /** @var array|null $parentsList holds the list of parents models for this model*/
    private $parentsList;

    /** @var object|null $parents holds the parents objects*/
    public $parents;

    /** @var array|null $domainList holds the domains specifications*/
    private $domainList;

    /** @var object|null $domainValue holds the descriptive values of each domain*/
    public $domainValue;

	/**
     * DataLayer constructor.
     * @param string $entity
     * @param array $required
     * @param string $primary
     * @param bool $timestamps
     */
    public function __construct(string $entity, array $required, string $primary = 'id', bool $timestamps = true)
    {
        $this->entity = $entity;
        $this->primary = $primary;
        $this->required = $required;
        $this->timestamps = $timestamps;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (empty($this->data)) {
            $this->data = new stdClass();
        }

        $this->data->$name = $value;
        
        $this->refreshDomainsValues();

    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data->$name);
    }

    /**
     * @param $name
     * @return string|null
     */
    public function __get($name)
    {
        return ($this->data->$name ?? null);
    }

    /**
     * @return object|null
     */
    public function data(): ?object
    {
        return $this->data;
    }

    /**
     * @return PDOException|Exception|null
     */
    public function fail()
    {
        return $this->fail;
    }

    /**
     * @param string|null $terms
     * @param string|null $params
     * @param string $columns
     * @return DataLayer
     */
    public function find(?string $terms = null, ?string $params = null, string $columns = "*"): DataLayer
    {
        if ($terms) {
            $this->statement = "SELECT {$columns} FROM {$this->entity} WHERE {$terms}";
            parse_str($params, $this->params);
            return $this;
        }

        $this->statement = "SELECT {$columns} FROM {$this->entity}";
        return $this;
    }

    /**
     * @param int $id
     * @param string $columns
     * @return DataLayer|null
     */
    public function findById(int $id, string $columns = "*"): ?DataLayer
    {
        $find = $this->find($this->primary . " = :id", "id={$id}", $columns);
        return $find->fetch();
    }

    /**
     * @param string $column
     * @return DataLayer|null
     */
    public function group(string $column): ?DataLayer
    {
        $this->group = " GROUP BY {$column}";
        return $this;
    }

    /**
     * @param string $columnOrder
     * @return DataLayer|null
     */
    public function order(string $columnOrder): ?DataLayer
    {
        $this->order = " ORDER BY {$columnOrder}";
        return $this;
    }

    /**
     * @param int $limit
     * @return DataLayer|null
     */
    public function limit(int $limit): ?DataLayer
    {
        $this->limit = " LIMIT {$limit}";
        return $this;
    }

    /**
     * @param int $offset
     * @return DataLayer|null
     */
    public function offset(int $offset): ?DataLayer
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }

    /**
     * @param bool $all
     * @return array|mixed|null
     */
    public function fetch(bool $all = false)
    {
        try {
            $stmt = Connect::getInstance()->prepare($this->statement . $this->group . $this->order . $this->limit . $this->offset);
            $stmt->execute($this->params);
            // parse_str($this->params, $arrayParams);
            // $stmt->execute($arrayParams);

            if (!$stmt->rowCount()) {
                return null;
            }

            if ($all) {
                return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
            }

            return $stmt->fetchObject(static::class);
        } catch (PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * @return int
     */
    public function count(): int
    {
        $stmt = Connect::getInstance()->prepare($this->statement);
        $stmt->execute($this->params);
        // parse_str($this->params, $arrayParams);
        // $stmt->execute($arrayParams);
        return $stmt->rowCount();
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        $primary = $this->primary;
        $id = null;

        try {
            if (!$this->required()) {
                throw new Exception("Preencha os campos necessários");
            }

            /** Update */
            if (!empty($this->data->$primary)) {
                $id = $this->data->$primary;
                $this->update($this->safe(), $this->primary . " = :id", "id={$id}");
            }

            /** Create */
            if (empty($this->data->$primary)) {
                $id = $this->create($this->safe());
            }

            if (!$id) {
                return false;
            }

            $this->data = $this->findById($id)->data();
            return true;
        } catch (Exception $exception) {
            $this->fail = $exception;
            return false;
        }
    }

    /**
     * @return bool
     */
    public function destroy(): bool
    {
        $primary = $this->primary;
        $id = $this->data->$primary;

        if (empty($id)) {
            return false;
        }

        $destroy = $this->delete($this->primary . " = :id", "id={$id}");
        return $destroy;
    }

    /**
     * @return bool
     */
    protected function required(): bool
    {
        $data = (array)$this->data();
        foreach ($this->required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return array|null
     */
    protected function safe(): ?array
    {
        $safe = (array)$this->data;
        unset($safe[$this->primary]);

        return $safe;
    }

    /**
     * @param string $model
     * @param string $co_field
     * @return array|mixed|null
     */
    protected function newParent(string $co_field , string $model, string $alias="" ) 
    {
        if(!isset($this->parentsList)) {
			$this->parentsList = [];
		}
        $newParent = new stdClass();
        $newParent->co_field = $co_field;
        $newParent->model = $model;
        $newParent->alias = $alias=="" ? $co_field : $alias;
        array_push($this->parentsList,$newParent);
    }

     /**
     * 
     */
    public function fetchParents() 
    {
        $primary = $this->primary;
        if(!($this->$primary>0)){
            return;
        }
        if(!isset($this->parentsList)) {
			return;
		}
        foreach ($this->parentsList as $parent) {
            $model = $parent->model;
            $co_field = $parent->co_field;
            $alias = $parent->alias;
            if($this->$co_field>0) {
                $model = new $model;
                $model = $model->findById($this->$co_field);
                if(!isset($this->parents)) {
                    $this->parents = new stdClass();
                }
                $this->parents->$alias = $model;
            }
        }
    }

    /**
     * @param string $model
     * @param string $co_field
     * @return array|mixed|null
     */
    protected function newDomain(string $field , array $domains, string $default="" ) 
    {
        if(!isset($this->domainList)) {
			$this->domainList = [];
		}
        $newDomain = new stdClass();
        $newDomain->field = $field;
        $newDomain->options = $domains;
        $newDomain->default = $default; 
        $this->domainList[$field] = $newDomain;
        // array_push($this->domainList,$newDomain);

        if(!isset($this->$field)){
            $this->$field = $default;
        }

        $this->refreshDomainsValues();
    }

    /**
     * @param string $field
     * @return array|null
     */    
    public function listDomain(string $field): ?array
    {
        $domain = $this->domainList[$field];
        $list = [];
        foreach ($domain->options as $key=>$value) {
            $item = new stdClass();
            $item->key = $key;
            $item->description = $value;
            $item->selected = $this->$field == $key ? "Y" : "";
            $list[$key]=$item;
        }
        return $list;
    }

    /**
     * 
     * 
     */        
    protected function refreshDomainsValues()
    {
        $this->domainValue = new stdClass();
        if(!isset($this->domainList)){
            return;
        }
        foreach ($this->domainList as $domain) {
            $field = $domain->field;
            if(isset($this->$field)) {
                foreach ($domain->options as $key => $value) {
                    if($this->$field == $key) {
                        $this->domainValue->$field = $value;
                    }
                }
            }
        }
    }




}
