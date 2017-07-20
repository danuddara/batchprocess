<?php

namespace Drupal\trutest_distributor_dealer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Distributor import form.
 */
class DealerImportForm extends FormBase {
  

    
    
  private $config;


  /**
   * Config object setup for enquiry cart settings.
   */
  public function __construct() {

    $this->config = $this->config('distributor_dealer.settings');
  
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dealer_import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {



    $form['break'] = [
      '#type' => 'markup',
      '#markup' => '<div class="row">Are you sure you want to import Dealers?. </div>',
    ];

    $form['submit'] = [
      '#prefix' => '<div class="row clearfix">',
      '#suffix' => '</div>',
      '#type' => 'submit',
      '#value' =>'Import Dealers',
      '#attributes' => ['class' => ['btn-primary']],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

      
      if(!empty($this->config->get('brand_selection_coloumn'))){
          
                $batch = [
                'title'            => t('Importing CSV ...'),
                'operations'       => [],
                'init_message'     => t('Commencing'),
                'progress_message' => t('Processed @current out of @total.'),
                'error_message'    => t('An error occurred during processing'),
                'finished'         => 'csvimport_import_finished',
                'file'             => drupal_get_path('module', 'trutest_distributor_dealer') . '/csvimport.batch.inc',
              ];
                  // dpm($batch);
                   //dpm(chr(9));
                if ($csvupload = $this->config->get('importfilePath.dealer')) {



                if ($handle = @fopen($csvupload, 'r')) {

                  $batch['operations'][] = [
                    '_csvimport_remember_init',
                    [$csvupload,'Dealer'],
                  ];

                  //loop through file line by line
                  while ($line = fgetcsv($handle, 2048, chr(9))) {

                     // $line_cleaned = array_map('base64_encode', $line);
                    //  $line_cleaned_decode = array_map('base64_decode', $line_cleaned);
                    //  dpm($line_cleaned_decode);
                    /**
                     * Use base64_encode to ensure we don't overload the batch
                     * processor by stuffing complex objects into it.
                     */
                   $brand_col=$this->config->get('brand_selection_coloumn');
                   if(!empty($line[$brand_col])){   
                      $batch['operations'][] = [
                         '_csvimport_import_line',
                         [array_map('base64_encode', $line),$this->config->get('brand_selection_coloumn'),'Dealer'],
                       ];
                   }
                  }

                  fclose($handle);
                  
                  batch_set($batch);

                }
                else
                {
                    drupal_set_message($this->t('Could not open the file to import.Please check the file path configured in settings.'),'error');
                }
                // we caught this in csvimport_form_validate()
              } // we caught this in csvimport_form_validate()

             

    
        }
        else
        {
            drupal_set_message($this->t('You have not setup your configurations properly in settings tab. Please select the brand and save configuration'), 'error');
        }

  }

}
