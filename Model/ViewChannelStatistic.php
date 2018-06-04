<?php
App::uses('AppModel', 'Model');

class ViewChannelStatistic extends AppModel
{
   public $actsAs = ['Containable'];

   public $belongsTo = [
      'Channel' =>
         [
            'className' => 'Channel.Channel',
            'foreignKey' => 'channel_id'
         ]
   ];
}