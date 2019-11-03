<?php

class DBI_Log_Entry {
    /**
     * @var string
     */
    private $operation;
    /**
     * @var string
     */
    private $entity;
    /**
     * @var int
     */
    private $result;
    /**
     * @var int
     */
    private $row_id;
    /**
     * @var string
     */
    private $details;

    public function __construct(
        string $operation, string $entity,
        int $result, int $row_id, string $details)
    {
        $allowed_ops = ['LOGIN', 'LOGOUT', 'INSERT', 'UPDATE', 'DELETE'];
        if (!in_array($operation, $allowed_ops)) {
            throw new InvalidArgumentException(
                'operation must be one of ' . implode(', ', $allowed_ops) .
                'but is ' . $operation);
        }

        if (empty($entity)) {
            throw new InvalidArgumentException(
                'entity is empty but is required');
        }
        $this->operation = $operation;
        $this->entity    = $entity;
        $this->result    = $result;
        $this->row_id    = $row_id;
        $this->details   = $details;
    }

    public function store() {
        global $dbi;
        return $dbi->log( $this );
    }

    public function &__get( string $name )
    {
        return $this->{$name};
    }

    public function __isset( string $name )
    {
        return isset( $this->{$name} );
    }

    public function __set( string $name, $value )
    {
        throw new BadMethodCallException( "DBI_Log_Entry is read-only.
            Attributes are immutable." );
    }
    public function __unset( string $name )
    {
        throw new BadMethodCallException( "DBI_Log_Entry is read-only.
            Attributes are immutable." );
    }
}