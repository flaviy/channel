<?php
App::uses('AppModel',      'Model');

class ChannelType extends AppModel
{
   
   /**
    * @return array
    */
   public static function getChannelTypes()
   {
      return AppModel::getInstance('Channel.ChannelType')->find('list');
   }
}