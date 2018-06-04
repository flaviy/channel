<?php
App::uses('AppModel', 'Model');

class ChannelStatistic extends AppModel
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