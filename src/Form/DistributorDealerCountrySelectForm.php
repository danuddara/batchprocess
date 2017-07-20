<?php

namespace Drupal\trutest_distributor_dealer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Locale\CountryManager;
use Symfony\Component\HttpFoundation\RequestStack;

class DistributorDealerCountrySelectForm extends FormBase {
    
    private $country_selected;


    public function __construct() {
        $route_match = \Drupal::service('current_route_match'); //get current route match
        $this->country_selected = $route_match->getParameter('country'); //node is
       
    }

    /**
   * {@inheritdoc}
   */
    public function getFormId() {
        return 'country_select_dealer_distributor_form';
    }
    
   /**
   * {@inheritdoc}
   */
    public function buildForm(array $form, FormStateInterface $form_state) {
        
       $countries = CountryManager::getStandardList(); 
      //  dpm(array_keys($countries));
       // dpm(\Drupal::routeMatch()->getRouteName());
        $keys = array_keys($countries);
       
        $form['prefix']= [ '#prefix' => '<div class="row countryselectdistributordealer">',];
        
        $form['label']=['#prefix' => '<div class="col-sm-4"><span class="pull-right center-label">Find a Distributor or Dealer in your Country/Region</span>',
                    '#suffix' => '</div>',];
        
       $form['country'] = [
                    '#prefix' => '<div class="col-sm-4">',
                    '#suffix' => '</div>',
                    '#type' => 'select',
                    //'#title' => $this->t('Select the Brand to map with the import'),
                    '#options' => $countries,
                    '#default_value' => (!empty($this->country_selected))?$this->country_selected:'NZ',
                    //'#description' => $this->t('Select the Brand for the website.'),
                    '#required'=>true,
                  ];
       
        $form['submit'] = [
                    '#prefix' => '<div class="col-sm-4">',
                    '#suffix' => '</div>',
                    '#type' => 'submit',
                    '#value' =>'Go',
                    '#attributes' => ['class' => ['btn-primary']],
                  ];
        
         $form['suffix']= [ '#suffix' => '</div><div class="margin-bottom"></div>',];
        
        return $form;
        
    }
    
      /**
   * {@inheritdoc}
   */
    public function submitForm(array &$form, FormStateInterface $form_state) {
       $country=$form_state->getValue('country');
       //$this->redirect('contact.country', ['country'=>$country]);
             //$form_state->setRedirect($this->routeMatch->getRouteName(), ['country'=>$country]);
       $form_state->setRedirect('contact.country',['country'=>$country]);
       
    }
    
         
}


