<?php

/**
 * Listener
 * 
 * @author Alexandre Mogère
 */
class Doctrine_Template_Listener_DataInheritance extends Doctrine_Record_Listener
{
  protected $_options = array();
  
  public function __construct(array $options)
  {
    $this->_options = $options;
  }
  
  public function preUpdate(Doctrine_Event $event)
  {
    $event->getInvoker()->synchronizeParent();
  }
  
  public function preInsert(Doctrine_Event $event)
  {
    $event->getInvoker()->synchronizeParent(false);
  }
  
  public function preDelete(Doctrine_Event $event)
  {
    $event->getInvoker()->deleteOnCascade();
  }
}
