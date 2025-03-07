<?php

class ModelTransaction
{
    private $db;
    private $operations = [];
    private $results = [];
    private $state = 'initial'; // initial, started, committed, rolled_back

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function __get($key)
    {
        if (isset($this->results[$key])) {
            return $this->results[$key];
        }
        throw new Exception("No se encontró el resultado '$key' en la transacción");
    }

    private function validateState($expectedState, $errorMessage)
    {
        if ($this->state !== $expectedState) {
            throw new Exception($errorMessage);
        }
    }

    public function addOperation($key, callable $operation)
    {
        $this->validateState('initial', "No operations can be added after starting the transaction.");

        $this->operations[$key] = $operation;
        return $this;
    }

    public function start()
    {
        $this->validateState('initial', "The transaction has already been initiated.");

        $this->db->beginTransaction();
        $this->state = 'started';

        try {
            foreach ($this->operations as $key => $operation) {
                $this->results[$key] = $operation($this->results);
            }
            return $this;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function commit()
    {
        $this->validateState('started', "The transaction has not been initiated or has already been confirmed.");

        $this->db->commit();
        $this->state = 'committed';
        return $this->results;
    }

    public function rollback()
    {
        $this->validateState('started', "You cannot reverse a transaction that has not been initiated.");

        $this->db->rollBack();
        $this->state = 'rolled_back';
        return $this;
    }

    public function getResults()
    {
        return $this->results;
    }
}
