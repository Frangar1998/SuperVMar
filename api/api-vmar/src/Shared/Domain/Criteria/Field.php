<?php

namespace SuperVMar\Shared\Domain\Criteria;

use SuperVMar\Shared\Domain\TableNames;

readonly class Field
{
    public function __construct(
        private TableNames $tableName,
        private FieldName $fieldName,
    )
    {
    }

    public function tableName(): TableNames
    {
        return $this->tableName;
    }

    public function fieldName(): FieldName
    {
        return $this->fieldName;
    }

    public function __toString(): string
    {
        return sprintf('%s.%s', $this->tableName->value, $this->fieldName);
    }
}