<?php
// Autogenerated PersistentObject definition

$def = new ezcPersistentObjectDefinition();
$def->table = 'data';
$def->class = 'data';

$def->properties['boiling_temp_k']               = new ezcPersistentObjectProperty();
$def->properties['boiling_temp_k']->columnName   = 'boiling_temp_k';
$def->properties['boiling_temp_k']->propertyName = 'boiling_temp_k';
$def->properties['boiling_temp_k']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_FLOAT;


$def->properties['melting_temp_k']               = new ezcPersistentObjectProperty();
$def->properties['melting_temp_k']->columnName   = 'melting_temp_k';
$def->properties['melting_temp_k']->propertyName = 'melting_temp_k';
$def->properties['melting_temp_k']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_FLOAT;


$def->idProperty               = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName   = 'node_id';
$def->idProperty->propertyName = 'node_id';
$def->idProperty->generator    = new ezcPersistentGeneratorDefinition( 'ezcPersistentManualGenerator' );

return $def;

?>
