<?php

App::uses('AppModel', 'Model');
App::uses('Setting', 'Model');


class  SearchConfig extends AppModel
{
   public $useTable = false;

   protected static $searchConfigFields = [
      'max_results_per_search',
      'max_pages_per_search',
      'expanded_search_active',
      'max_labels_per_search',
      'max_labels_df',
      'max_current_place_distance'
   ];
   const CHANNEL_AR_SEARCH_SETTINGS_FIELD = 'channel_ar_search_settings';

   /**
    * @return array
    */
   public static function getSearchConfigFields()
   {
      return self::$searchConfigFields;
   }


   public function beforeValidate($options = [])
   {
      parent::beforeValidate($options);
      $distance_filters = array_keys(AppModel::getInstance('Channel.DistanceFilter')->getDistanceFilters());

      foreach (['max_results_per_search', 'max_pages_per_search', 'max_labels_per_search'] as $configField) {
         $this->validate[$configField] = [
               'allowEmpty' => true,
               'required' => 'create',
               'rule' => 'numeric',
               'message' => 'Please, provide valid numeric value.'
         ];
      }
      $this->validate['max_labels_df'] = [
         'required' => 'create',
         'allowEmpty' => true,
         'rule' => ['inList', $distance_filters],
         'message' => 'Incorrect input value.'
      ];
   }

   /**
    * @return bool
    * @throws Exception
    */
   public function saveSearchSettings()
   {
      if (!empty($this->data) && $this->validates()) {
         foreach ($this->data[$this->name] as $key => $value) {
            if (!in_array($key, self::getSearchConfigFields()) || empty($this->data[$this->name][$key])) {
               unset($this->data[$this->name][$key]);
            }
         }
         /** @var Setting $SettingModel */
         $SettingModel = parent::getInstance('Setting');
         $setting = $SettingModel->findByName(self::CHANNEL_AR_SEARCH_SETTINGS_FIELD);
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
    * @param null $fieldName
    * @return mixed|null
    */
   public function getSearchConfigValue($fieldName = null)
   {
      $SettingModel = parent::getInstance('Setting');
      $setting = $SettingModel->getConfigValue(self::CHANNEL_AR_SEARCH_SETTINGS_FIELD, 'channels_settings');
      if (!empty($setting)) {
         $return = unserialize($setting);
         if(!empty($fieldName)){
            if(!empty($return[$fieldName])){
               return $return[$fieldName];
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
   public function getSearchConfigValues()
   {
      $SettingModel = parent::getInstance('Setting');
      $setting = $SettingModel->getConfigValue(self::CHANNEL_AR_SEARCH_SETTINGS_FIELD, 'channels_settings');
      if (!empty($setting)) {
         $return = unserialize($setting);
         return $return;
      }
      return null;
   }
}