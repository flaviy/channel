<?php
App::uses('AppModel', 'Model');
App::uses('ChannelCategoryType', 'Channel.Model');
App::uses('ChannelGroup', 'Channel.Model');
App::uses('ChannelType', 'Channel.Model');

class Channel extends AppModel
{
   const WEBSITE_CHANNEL_SHORT_NAME = 'RWcom';
   const FACEBOOK_CHANNEL_SHORT_NAME = 'RWFB';
   public static $imgPath = 'channels/';
   public $useTable = 'channels';
   public $actsAs = ['Containable'];

   public $belongsTo = [
      'ChannelCategoryType' =>
         [
            'className' => 'Channel.ChannelCategoryType',
            'foreignKey' => 'channel_category_type_id'
         ],
      'ChannelGroup' =>
         [
            'className' => 'Channel.ChannelGroup',
            'foreignKey' => 'channel_group_id'
         ],
      'ChannelType' =>
         [
            'className' => 'Channel.ChannelType',
            'foreignKey' => 'channel_type_id'
         ]
   ];
   public $validate = [
      'title' => ['notBlank' => ['rule' => 'notBlank', 'required' => 'create', 'allowEmpty' => false], 'unique' => ['rule' => 'isUnique', 'message' => 'Must be unique']],
      'short_name' => ['notBlank' => ['rule' => 'notBlank', 'required' => 'create', 'allowEmpty' => false], 'unique' => ['rule' => 'isUnique', 'message' => 'Must be unique']],
      'logo' => [
         'format' => [
            'allowEmpty' => true,
            'required' => false,
            'rule' => [
               'extension',
               ['gif', 'jpeg', 'png', 'jpg']
            ],
            'message' => 'Please supply a valid image.'
         ],
         'maxSize' => [
            'rule' => ['fileSize', '<=', '3MB'],
            'message' => 'Image must be less than 3MB'
         ]
      ],
      'channel_type_id' => array(
         'allowEmpty' => true,
         'required' => 'create',
         'rule' => array('ruleChannelType'),
         'message' => 'Please, select channel type.'
      ),
      'channel_category_type_id' => array(
         'allowEmpty' => true,
         'required' => 'create',
         'rule' => array('ruleChannelCategoryType'),
         'message' => 'Please, select valid category type.'
      ),
      'channel_group_id' => array(
         'allowEmpty' => true,
         'required' => 'create',
         'rule' => array('ruleChannelGroup'),
         'message' => 'Please, select valid group.'
      )
   ];

   public static function getChannels()
   {
      return AppModel::getInstance('Channel.Channel')->find('list');
   }

   public function afterSave($created, $options = [])
   {
      if ($created) {
         $data = ['channel_id' => $this->id];
         return AppModel::getInstance('Channel.ChannelStatistic')->save($data);
      }
      return true;
   }

   public function beforeFind($query = [])
   {
      $query['conditions'][] = ['Channel.is_deleted' => 0];
      return $query;
   }

   public function ruleChannelType($check)
   {
      return in_array($check['channel_type_id'], array_keys(ChannelType::getChannelTypes()));
   }

   public function ruleChannelCategoryType($check)
   {
      return in_array($check['channel_category_type_id'], array_keys(ChannelCategoryType::getCategoryTypes()));
   }

   public function ruleChannelGroup($check)
   {
      return in_array($check['channel_group_id'], array_keys(ChannelGroup::getGroups()));
   }

   public function getIdByShortName($short_name)
   {
      $channel = $this->getByShortName($short_name);
      return $channel ? $channel[$this->name]['id'] : null;
   }

   public function getByShortName($short_name)
   {
      return $this->find('first', ['conditions' => ['short_name' => $short_name,], 'contain' => []]);
   }
}