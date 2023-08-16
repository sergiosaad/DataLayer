<?php

namespace SergioSaad\DataLayer;

use DateTime;
use Exception;
use PDOException;

/**
 * Trait CrudTrait
 * @package SergioSaad\DataLayer
 */
trait CrudTrait
{
    /**
     * @param array $data
     * @return int|null
     * @throws Exception
     */
    protected function create(array $data): ?int
    {
        if ($this->timestamps) {
            $data["dt_created"] = (new DateTime("now"))->format("Y-m-d H:i:s");
            if(!key_exists("nm_created",$data)){
                $data["nm_created"] = "automatic";
            }

        }

        try {
            $columns = implode(", ", array_keys($data));
            $values = ":" . implode(", :", array_keys($data));

            $stmt = Connect::getInstance()->prepare("INSERT INTO {$this->entity} ({$columns}) VALUES ({$values})");
            $stmt->execute($this->filter($data));

            return Connect::getInstance()->lastInsertId();
        } catch (PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * @param array $data
     * @param string $terms
     * @param string $params
     * @return int|null
     * @throws Exception
     */
    protected function update(array $data, string $terms, string $params): ?int
    {
        if ($this->timestamps) {
            $data["dt_edited"] = (new DateTime("now"))->format("Y-m-d H:i:s");
            $data["nm_edited"] = key_exists("nm_edited",$data) ? $data["nm_edited"] : "";
            $data["nm_edited"] = $data["nm_edited"]=="" ? "automatic - not informed" : $data["nm_edited"];
        }

        try {
            $dateSet = [];
            foreach ($data as $bind => $value) {
                $dateSet[] = "{$bind} = :{$bind}";
            }
            $dateSet = implode(", ", $dateSet);
            parse_str($params, $params);

            $stmt = Connect::getInstance()->prepare("UPDATE {$this->entity} SET {$dateSet} WHERE {$terms}");
            $stmt->execute($this->filter(array_merge($data, $params)));
            return ($stmt->rowCount() ?? 1);
        } catch (PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * @param string $terms
     * @param string|null $params
     * @return bool
     */
    public function delete(string $terms, ?string $params): bool
    {
        try {
            $stmt = Connect::getInstance()->prepare("DELETE FROM {$this->entity} WHERE {$terms}");
            if ($params) {
                parse_str($params, $params);
                $stmt->execute($params);
                //parse_str($params, $arrayParams);
                //$stmt->execute($arrayParams);
                return true;
            }

            $stmt->execute();
            return true;
        } catch (PDOException $exception) {
            $this->fail = $exception;
            return false;
        }
    }

    /**
     * @param array $data
     * @return array|null
     */
    private function filter(array $data): ?array
    {
        $filter = [];
        foreach ($data as $key => $value) {
            $filter[$key] = (is_null($value) ? null : filter_var($value, FILTER_DEFAULT));
        }
        return $filter;
    }
}