<?php

/**
 * Full Concrete Table Inheritance Behavior
 *
 * Doctrine Concrete Inheritance copies only structure of parent table.
 * This behavior allows to have a data replication.
 *
 * @author Alexandre Mogère
 */
class Doctrine_Template_DataInheritance extends Doctrine_Template
{
  /**
   * Array of DataInheritance options
   *
   * @var string
   */
  protected $_options = array(
    'descendant_column' => 'descendant_class',
    'parent_alias'      => 'Parent'
  );

  /**
   * __construct
   *
   * @param string $array
   * @return void
   */
  public function __construct(array $options = array())
  {
    $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
  }
  
  public function setTableDefinition()
  {
    $parent = array_reverse($this->_table->getOption('parents'));
    $i = 0;
    // be sure that you do not instanciate an abstract class;
    $reflectionClass = new ReflectionClass($parent[$i]);
    while ($reflectionClass->isAbstract())
    {
      $i++;
      $reflectionClass = new ReflectionClass($parent[$i]);
    }
    
    $this->_options['extends'] = $parent[$i];
    
    // creates a one-to-one relationship between a object and its parent
    $this->hasOne("{$this->_options['extends']} as {$this->_options['parent_alias']}", array(
      'local' => 'parent_id',
      'foreign' => 'id',
      'onDelete' => 'cascade'
    ));

    $this->_table->setColumn('parent_id', 'integer', 20, array(
      'type' => 'integer',
      'length' => 20,
    ));
    
    $this->addListener(new Doctrine_Template_Listener_DataInheritance($this->_options));
  }
  
  /**
   * Cleans replicated data
   *
   * @return boolean
   */
  public function deleteOnCascade()
  {
    if (! is_null($this->_invoker->parent_id))
    {
      $parent = Doctrine_Core::getTable($this->_options['extends'])->find($this->_invoker->parent_id);
      
      if ($parent)
      {
        return $parent->delete();
      }
    }
    
    return false;
  }
  
  /**
   * synchronizeParent
   *
   * @param boolean $update
   * @return void
   */
  public function synchronizeParent($update = true)
  {
    $parent = null;
    $parentClass = $this->_options['extends'];
    if ($update)
    {
      $fieldsModified = $this->_invoker->getModified(true);
      
      if (! empty($fieldsModified))
      {
        $method = "get{$this->_options['parent_alias']}";
        $parent = $this->_invoker->$method();
      }
    }
    else if (is_null($this->_invoker->parent_id))
    {
      $parent = new $parentClass;
    }
    
    if (! is_null($parent))
    {
      $this->buildParentFromFields($parent);
    }
  }
  
  /**
   * buildParentFromFields
   *
   * @param Doctrine_Table $parent
   * @return void
   */
  protected function buildParentFromFields($parent)
  {
    $parentClass = $this->_options['extends'];
    $parentTable = Doctrine_Core::getTable($parentClass);
    
    $colsParent = $parentTable->getColumns();
    foreach ($colsParent as $column => $definition)
    {
      $fieldName = $this->getTable()->getFieldName($column);
      
      if (in_array($fieldName, array(
        'id',
        'parent_id',
        $this->_options['descendant_column']
      )))
      {
        continue;
      }

      $parent->$fieldName = $this->_invoker->$fieldName;
    }
   
    $parent->{$this->_options['descendant_column']} = $this->getTable()->getTableName();
    $parent->save();
    
    $this->_invoker->parent_id = $parent->id;
  }

}