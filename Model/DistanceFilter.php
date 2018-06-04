<?php

App::uses('AppModel', 'Model');
App::uses('Setting', 'Model');


class  DistanceFilter extends AppModel
{
   public $useTable = false;

   protected static $filterNames = [
      'right_here',
      'nearby',
      'neighborhood',
      'city'
   ];

   /**
    * @return array
    */
   public static function getFilterNames()
   {
      return self::$filterNames;
   }

   public function beforeValidate($options = [])
   {
      parent::beforeValidate($options);
      foreach (self::$filterNames as $filterName) {
         $this->validate[$filterName] = [
               'allowEmpty' => true,
               'required' => 'create',
               'rule' => 'numeric',
               'message' => 'Please, provide valid numeric value.'
         ];
      }
   }

   /**
    * @return bool
    */
   public function saveFilterValues()
   {
      if (!empty($this->data) && $this->validates()) {
         foreach ($this->data[$this->name] as $key => $value) {
            if (!in_array($key, self::getFilterNames()) || empty($this->data[$this->name][$key])) {
               unset($this->data[$this->name][$key]);
            }
         }
         /** @var Setting $SettingModel */
         $SettingModel = parent::getInstance('Setting');
         $setting = $SettingModel->findByName('ar_distance_filters');
         if(!empty($setting)){
            $SettingModel->id = $setting['Setting']['id'];
            $SettingModel->set(['value' => serialize($this->data[$this->name])]);
            if($SettingModel->save()){
               return true;
            }
         }
      }
      return false;
   }

   /**
    * @param null $filterName
    * @return mixed|null
    */
   public function getDistanceFilterValue($filterName = null)
   {
      $SettingModel = parent::getInstance('Setting');
      $setting = $SettingModel->getConfigValue('ar_distance_filters', 'channels_settings');
      if (!empty($setting)) {
         $return = unserialize($setting);
         if(!empty($filterName)){
            if(!empty($return[$filterName])){
               return $return[$filterName];
            }
         } else {
            return $return;
         }
      }
      return null;
   }

   /**
    * @return mixed|null
    */
   public function getDistanceFilters()
   {
      $SettingModel = parent::getInstance('Setting');
      $setting = $SettingModel->getConfigValue('ar_distance_filters', 'channels_settings');
      if (!empty($setting)) {
         $return = unserialize($setting);
         return $return;
      }
      return null;
   }
}