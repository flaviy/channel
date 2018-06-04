<?php
App::uses('AppModel',      'Model');

class ChannelGroup extends AppModel
{
   
   /**
    * @return array
    */
   public static function getGroups() 
   {
      return AppModel::getInstance('Channel.ChannelGroup')->find('list');
   }
}