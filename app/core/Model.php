<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/ModelTransaction.php';

#[AllowDynamicProperties]
class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    private $hasTimestamps = false;
    private $hasSoftDelete = false;
    private $tableMetadataCache = [];

    public function __construct($data = null)
    {
        $this->fill($data ?? $this->getDefaultValues());
    }

    private function initialize()
    {
        if (!isset($this->db)) {
            $this->db = Database::getInstance()->getConnection();
            if (!empty($this->table)) {
                $this->loadTableMetadata();
            }
        }
    }

    private function fill($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $this->$key = $value;
            }
        }
        return $this;
    }

    private function loadTableMetadata()
    {
        $class = get_class($this);
        if (!isset($this->tableMetadataCache[$class])) {
            $columns = $this->fetchTableColumns();
            $this->tableMetadataCache[$class] = [
                'columns' => $columns,
                'timestamps' => in_array('created_at', $columns) && in_array('updated_at', $columns),
                'softDelete' => in_array('deleted_at', $columns)
            ];
        }

        $this->hasTimestamps = $this->tableMetadataCache[$class]['timestamps'];
        $this->hasSoftDelete = $this->tableMetadataCache[$class]['softDelete'];
    }

    private function fetchTableColumns()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SHOW COLUMNS FROM `" . $this->table . "`");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function executeQuery($query, array $params = [])
    {
        $this->initialize();
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    private function sanitizeData(array $data)
    {
        $filtered = $this->fillable
            ? array_intersect_key($data, array_flip($this->fillable))
            : $data;

        if ($this->hasTimestamps) {
            unset($filtered['created_at'], $filtered['updated_at']);
        }

        if ($this->hasSoftDelete) {
            unset($filtered['deleted_at']);
        }

        return $this->applyCasts($filtered);
    }

    private function applyCasts(array $data)
    {
        $casts = $this->casts();
        foreach ($data as $key => &$value) {
            if (!isset($casts[$key]) || $value === null) continue;

            $value = match ($casts[$key]) {
                'int', 'integer' => (int) $value,
                'float', 'double', 'real' => (float) $value,
                'bool', 'boolean' => (bool) $value,
                'string' => (string) $value,
                'json' => is_string($value) ? $value : json_encode($value),
                'date' => date('Y-m-d', is_numeric($value) ? $value : strtotime($value)),
                'datetime' => date('Y-m-d H:i:s', is_numeric($value) ? $value : strtotime($value)),
                'timestamp' => is_numeric($value) ? $value : strtotime($value),
                'hashed' => password_hash($value, PASSWORD_BCRYPT, ['cost' => 12]),
                default => str_starts_with($casts[$key], 'decimal:')
                    ? number_format((float) $value, (int) substr($casts[$key], 8), '.', '')
                    : $value,
            };
        }
        return $data;
    }

    private function applyHiddenAttributes(array $data)
    {
        return $this->hidden ? array_diff_key($data, array_flip($this->hidden)) : $data;
    }

    private function hydrateResults(array $rows)
    {
        return array_map(fn($row) => new self($this->applyHiddenAttributes($row)), $rows);
    }

    private function buildWhereClause(array $conditions, array &$params)
    {
        if (empty($conditions)) {
            return '';
        }

        $whereClauses = array_map(fn($col) => "`$col` = :$col", array_keys($conditions));
        $params = array_merge($params, $conditions);

        return " WHERE " . implode(" AND ", $whereClauses);
    }

    private function addSoftDeleteClause($query, $hasWhere)
    {
        if (!$this->hasSoftDelete) {
            return $query;
        }

        return $query . ($hasWhere ? " AND" : " WHERE") . " deleted_at IS NULL";
    }

    protected function getDefaultValues()
    {
        return $this->fillable ? array_fill_keys($this->fillable, null) : [];
    }

    protected function casts()
    {
        return [];
    }

    public static function findByCondition(array $conditions = [], bool $single = false)
    {
        $instance = new static();

        $instance->initialize();
        try {
            $query = "SELECT * FROM `" .  $instance->table . "`";
            $params = [];

            $whereClause =  $instance->buildWhereClause($conditions, $params);
            $query .= $whereClause;

            $query =  $instance->addSoftDeleteClause($query, !empty($conditions));

            $stmt =  $instance->executeQuery($query, $params);

            if ($single) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result ? new self($instance->applyHiddenAttributes($result)) : null;
            } else {
                return $instance->hydrateResults($stmt->fetchAll(PDO::FETCH_ASSOC));
            }
        } catch (Exception $e) {
            error_log("Database query error: " . $e->getMessage());
            return $single ? null : [];
        }
    }

    public static function findByConditionOrFail(array $conditions = [], bool $single = false)
    {
        $result = static::findByCondition($conditions, $single);
        if (($single && !$result) || (!$single && empty($result))) {
            throw new Exception("Record not found");
        }
        return $result;
    }

    public static function find($id)
    {
        $instance = new static();
        return static::findByCondition([$instance->primaryKey => $id], true);
    }

    public static function findOrFail($id)
    {
        $result = static::find($id);
        if (!$result) {
            throw new Exception("Record with ID $id not found");
        }
        return $result;
    }

    public static function findAll()
    {
        return static::findByCondition();
    }

    public static function findAllByCondition(array $conditions)
    {
        return static::findByCondition($conditions);
    }

    public static function findAllOrFail()
    {
        $results = static::findAll();
        if (empty($results)) {
            throw new Exception("No records found");
        }
        return $results;
    }

    public static function create(array $data, bool $with_transaction = true)
    {
        $instance = new static();

        $instance->initialize();
        try {
            if ($with_transaction) $instance->db->beginTransaction();

            $data = $instance->sanitizeData($data);

            if ($instance->hasTimestamps) {
                $now = date('Y-m-d H:i:s');
                $data['created_at'] = $data['updated_at'] = $now;
            }

            $columns = implode("`, `", array_keys($data));
            $placeholders = implode(", ", array_map(fn($k) => ":$k", array_keys($data)));
            $query = "INSERT INTO `" . $instance->table . "` (`$columns`) VALUES ($placeholders)";

            $instance->executeQuery($query, $data);
            $id = $instance->db->lastInsertId();

            if ($with_transaction) $instance->db->commit();

            return static::find($id);
        } catch (Exception $e) {
            if ($with_transaction) $instance->db->rollBack();
            error_log("Error creating record: " . $e->getMessage());
            throw new Exception("Failed to create record: " . $e->getMessage());
        }
    }

    public static function update($id, array $data, bool $with_transaction = true)
    {
        $instance = new static();

        $instance->initialize();
        try {
            if ($with_transaction) $instance->db->beginTransaction();

            $data = $instance->sanitizeData($data);

            if ($instance->hasTimestamps) {
                $data['updated_at'] = date('Y-m-d H:i:s');
            }

            $setClause = implode(", ", array_map(fn($key) => "`$key` = :$key", array_keys($data)));
            $data[$instance->primaryKey] = $id;

            $query = "UPDATE `" . $instance->table . "` SET $setClause WHERE `" . $instance->primaryKey . "` = :" . $instance->primaryKey;

            $stmt = $instance->executeQuery($query, $data);
            $rowCount = $stmt->rowCount();

            if ($with_transaction) $instance->db->commit();

            return $rowCount > 0;
        } catch (Exception $e) {
            if ($with_transaction) $instance->db->rollBack();
            error_log("Error updating record: " . $e->getMessage());
            throw new Exception("Failed to update record: " . $e->getMessage());
        }
    }

    public static function delete($id, bool $with_transaction = true)
    {
        $instance = new static();

        $instance->initialize();
        try {
            if ($with_transaction) $instance->db->beginTransaction();

            if ($instance->hasSoftDelete) {
                static::update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
            } else {
                $query = "DELETE FROM `" . $instance->table . "` WHERE `" . $instance->primaryKey . "` = :id";
                $instance->executeQuery($query, ['id' => $id]);
            }

            if ($with_transaction) $instance->db->commit();

            return true;
        } catch (Exception $e) {
            if ($with_transaction) $instance->db->rollBack();
            error_log("Error deleting record: " . $e->getMessage());
            throw new Exception("Failed to delete record: " . $e->getMessage());
        }
    }

    public static function restore($id)
    {
        $instance = new static();

        $instance->initialize();
        if (!$instance->hasSoftDelete) {
            return false;
        }

        return static::update($id, ['deleted_at' => null]);
    }

    public static function beginTransaction(array $operations = [])
    {
        $instance = new static();

        $instance->initialize();
        $transaction = new ModelTransaction($instance->db);

        foreach ($operations as $key => $callable) {
            $transaction->addOperation($key, $callable);
        }

        return $transaction;
    }

    public static function executeTransaction(array $operations)
    {
        $transaction = static::beginTransaction($operations);
        try {
            $transaction->start();
            return $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }
}
