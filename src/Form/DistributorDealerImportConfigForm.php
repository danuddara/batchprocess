<?php

namespace Drupal\trutest_distributor_dealer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
/**
 * Distributor,Dealer config form.
 */
class DistributorDealerImportConfigForm extends ConfigFormBase
{
    private $config;

   /**
    * Constructor to set the config.
    */
    public function __construct() {
      //parent::__construct($config_factory);  
      $this->config = $this->config('distributor_dealer.settings');

    }

    public function getFormId() {
        return 'Distributor_Dealer_import_config_form';
    }
    
    public function getEditableConfigNames() {
        return [
            'distributor_dealer.settings',
          ];
    }
    
    public function buildForm(array $form, FormStateInterface $form_state) {
        
          $tru_test_brand_selection_column_array = array(
                '' => '', // no brand selected
		'18' => 'milkmeters', // 'milkmeters',
		'19' => 'stockmanagement', // 'scales',
		'20' => 'speedrite', // 'speedrite',
		'21' => 'stafix', // 'stafix',
		'22' => 'pel', // 'pel',
		'23' => 'trutestgroup', // 'trutestgroup',
		'24' => 'hayes', // 'hayes',
		'25' => 'patriot', // 'patriot',
			'29' => 'stafix_security', // 'stxsec',
			'30' => 'speedrite_security', // 'spesec',
		);
        
        //dsm($this->config->get('brand_selection_coloumn'));
           $form['brand_selection_coloumn'] = [
                    '#type' => 'select',
                    '#title' => $this->t('Select the Brand to map with the import'),
                    '#options' => $tru_test_brand_selection_column_array,
                    '#default_value' => $this->config->get('brand_selection_coloumn'),
                    '#description' => $this->t('Select the Brand for the website.'),
                    '#required'=>true,
                  ]; 
        
          $form['distributor_import_file_path'] = [
                    '#type' => 'textfield',
                    '#title' => $this->t('Tru-Test Distributors Data file location'),
                    '#default_value' => $this->config->get('importfilePath.distributor'),
                    '#description' => $this->t('Type in the path to the Trutest Distributor Data file.'),
                    '#required'=>true,
                  ]; 
          
           $form['dealer_import_file_path'] = [
                    '#type' => 'textfield',
                    '#title' => $this->t('Tru-Test Dealer Data file location'),
                    '#default_value' => $this->config->get('importfilePath.dealer'),
                    '#description' => $this->t('Type in the path to the Trutest Dealer Data file.'),
                    '#required'=>true,
                  ];
        
        return parent::buildForm($form, $form_state);
    }
    
    public function validateForm(array &$form, FormStateInterface $form_state) {
        
        if(empty($form_state->getValue('distributor_import_file_path')) or empty($form_state->getValue('dealer_import_file_path')) or empty($form_state->getValue('brand_selection_coloumn')))
        {
            $message = $this->t('The Path cannot be empty');
            $message_brands = $this->t('You must select the Brand for this website');
            (empty($form_state->getValue('distributor_import_file_path')))? $form_state->setErrorByName('distributor_import_file_path', $message):null;
            (empty($form_state->getValue('dealer_import_file_path')))? $form_state->setErrorByName('dealer_import_file_path', $message): null;
            (empty($form_state->getValue('brand_selection_coloumn')))? $form_state->setErrorByName('brand_selection_coloumn', $message_brands): null;
                   
           
        }
       if(strpos( $form_state->getValue('dealer_import_file_path'),'.tsv')==false)
        {
            
            $form_state->setErrorByName('dealer_import_file_path', $this->t('This Importer can only read tsv files. Please specify tsv files only'));
        }
       if(strpos( $form_state->getValue('distributor_import_file_path'),'.tsv')==false)
        {
             $form_state->setErrorByName('distributor_import_file_path', $this->t('This Importer can only read tsv files. Please specify tsv files only'));
        }
        
        parent::validateForm($form, $form_state);
    }

        public function submitForm(array &$form, FormStateInterface $form_state) {
        
         // Retrieve the configuration.
    $this->config
        // Set the submitted configuration setting.
      ->set('importfilePath.distributor', $form_state->getValue('distributor_import_file_path'))
        // You can set multiple configurations at once by making
        // multiple calls to set()
      ->set('importfilePath.dealer', $form_state->getValue('dealer_import_file_path'))
      ->set('brand_selection_coloumn',$form_state->getValue('brand_selection_coloumn'))      
      ->save();
        
        parent::submitForm($form, $form_state);
    }
    
}
