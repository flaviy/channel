<?php
App::uses('AppModel',      'Model');

class ChannelCategoryType extends AppModel
{
   
   /**
    * @return array
    */
   public static function getCategoryTypes()
   {
      return AppModel::getInstance('Channel.ChannelCategoryType')->find('list');
   }
}