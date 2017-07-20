<?php

namespace Drupal\trutest_distributor_dealer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Locale\CountryManager;
use \Drupal\node\Entity\Node;


class DealerImportController extends ControllerBase
{
    private $import_type;
    
    private $TRUTEST_DEALERS_IMPORT_MULTI_REGION_DELIMITER = '|';
    
    
    
    public function __construct() {
        $this->import_type = 'dealer';
    }

        //setbatch in formsubmit.
    //https://api.drupal.org/api/drupal/core%21includes%21form.inc/function/batch_process/8.2.x
    //https://www.drupal.org/docs/7/api/batch-api/overview
    
   
    
    public function importDealers($data,&$context)
    {
            
            $dealer_data =$this->dealers_import_map_data($data,$context);
            if (!empty($dealer_data)) {
                //dpm($dealer_data);
                
                //$this->tt_dealer_node_create($dealer_data, $context);
                
				$existing_node = $this->tt_dealer_iscreated($dealer_data);
				if(empty($existing_node) && ($dealer_data['field_jde_addr_type'] != 'XRS')) {
				 //create new node
                                 $this->tt_dealer_node_create($dealer_data, $context);
                                 $context['results']['new_imported']++; //new rows imported
				} else {
                                    
                                    //if there is an exisiting node
                                    if(!empty($existing_node)){
                                        
                                            //dpm($node->get('field_jde_date_updated')->getValue());
                                            $dateobject = $existing_node->get('field_jde_date_updated')->getString();
                                            $myDateTime1= \DateTime::createFromFormat('Y-m-d\TH:i:s', $dateobject);
                                            $myDateTime2= \DateTime::createFromFormat('Y-m-d\TH:i:s', $dealer_data['field_jde_date_updated']);
                                            $diffrence = date_diff($myDateTime1, $myDateTime2);

                                        $diffDays = $diffrence->y + $diffrence->m + $diffrence->d + $diffrence->h + $diffrence->i + $diffrence->s ; 

                                         if($diffDays!=0)
                                         {
                                             //delete if the new jde_add_value is XRS
                                             if($dealer_data['field_jde_addr_type']=='XRS'){


                                                $message = "{$existing_node->get('title')->getString()} was deleted from Dealers.";
                                                $existing_node->delete();
                                                \Drupal::logger('trutest_distributor_dealer')->notice($message);
                                                $context['results']['deleted_imported']++;
                                             }                            
                                             else{
                                             //else
                                             //do the update here
                                                 $this->tt_dealer_node_update($existing_node,$dealer_data);
                                                 $context['results']['updated_imported']++;
                                             }
                                         }
                                 
                                    }
					
				}
            }
    }
    
    private function tt_dealer_iscreated($dealer_data)
    {
        $node = null;
        $this->import_type; //dealer import query
        
        /**
         * if(JDE_ID)
         *  if(JDEIDupdated date < new JDE updated date)
         *          it's an update
         * else
         *  it's a create
         */
        
        $query = \Drupal::entityQuery('node');
        $query->condition('status', 1);
        $query->condition('type',$this->import_type);
        $query->condition('field_jde_addr_nbr', $dealer_data['field_jde_addr_nbr']);
        $query->range(0,1);
        $entity_ids = $query->execute();
       // dpm($entity_ids);
        
        if(!empty($entity_ids))
        {
            $nid = array_keys($entity_ids);
            //dpm($nid[0]);
           $node = Node::load($entity_ids[$nid[0]]);
           
           //$node->get('field_jde_date_updated');
          // dsm($node->get('field_jde_date_updated')->getString());
        }
        
       
        
        return $node;
    }
    
    
    private function tt_dealer_node_create($dealer_data, &$context)
    {
        
        
        $node = Node::create($dealer_data);
        $node->field_address->format = 'full_html'; //need to specify the format
        $node->save();//dpm($node);
        
    }
    
    private function tt_dealer_node_update($existing_node,$dealer_data)
    {
        $id = $existing_node->id();
        $node = Node::load($id);
        
        foreach($dealer_data as $key=>$value){
                $node->set($key,$value);
        }
        $node->field_address->format = 'full_html'; //always loose the format
        $node->save();      
       // dpm($node);
    }


    
    
    private function dealers_import_map_data($data,&$context)
    {
         $dealer_data = array();
           
                    $merchant_name = $data[1];

                  /*  $email_address = $data[14];
                    if (strpos($email_address, TRUTEST_DEALERS_IMPORT_MULTI_EMAIL_DELIMITER) !== FALSE)
                          $email_address = explode(TRUTEST_DEALERS_IMPORT_MULTI_EMAIL_DELIMITER, $email_address);*/

                    $regions = $data[17];
                    if (strpos($regions, $this->TRUTEST_DEALERS_IMPORT_MULTI_REGION_DELIMITER) !== FALSE){
                          $regions = explode($this->TRUTEST_DEALERS_IMPORT_MULTI_REGION_DELIMITER, $regions);
                    }
                    
                    if (!empty($merchant_name)) {
                          $dealer_data['type'] =  $this->import_type;
                          $dealer_data['title'] = $merchant_name;
                         // $dealer_data['company_name'] = $data[0];
                          $dealer_data['field_address'] = $this->dealers_import_get_address($data,$context);
                          $dealer_data['field_jde_rep_name'] = $data[16];
                          $dealer_data['field_jde_province'] = $regions;

                          /*
                          * Reference for Brand Selection in data
                          * Reference in brand details
                          */
                          $brands_column_array = array(
                          '18' => 'milkmeters', // 'milkmeters',
                          '19' => 'stockmanagement', // 'scales',
                          '20' => 'speedrite', // 'speedrite',
                          '21' => 'stafix', // 'stafix',
                          '22' => 'pel', // 'pel',
                          '23' => 'trutestgroup', // 'trutestgroup',
                          '24' => 'hayes', // 'hayes',
                          '25' => 'patriot', // 'patriot',
                                  '30' => 'stafix_security', // 'stxsec',
                                  '31' => 'speedrite_securi', // 'spesec',
                          );
                          foreach ($brands_column_array as $key => $value){
                                  $dealer_data['field_jde_brand_'.$value] = $data[$key];
                          }
                          
                        
                        
                          $dealer_data['field_jde_addr_nbr'] = $data[26];
                          $dealer_data['field_jde_addr_type'] = $data[27];
                          
                          $myDateTime= \DateTime::createFromFormat('Y-m-d H:i:s', $data[28]);
                          $dealer_data['field_jde_date_updated'] =  $myDateTime->format('Y-m-d\TH:i:s');
                          $country_code = $data[8];
                           if($country_code=='UK')
                            {
                                $country_code='GB';
                            }
                          //country
                          if (array_key_exists($country_code, CountryManager::getStandardList())){
                             $dealer_data['field_jde_addr_country'] = $country_code;
                          }
                    }
           
            return $dealer_data;
    }
    
    
    private function dealers_import_get_address($data,&$context) {
            $state = $data[6];
            $additional = $data[3];
            if (!empty($data[4])){
              $additional .= ', ' . $data[4]; 
            }
            $area_code = $data[9];
            $phone[] = $data[10];
            $phone[] = $data[11];
            $phone[] = $data[12];
            $fax = $data[13];
            
            if (!empty($area_code) && !empty($phone)) { foreach($phone as $key=>$value){ $phone[$key]="$area_code $value";}}
            if (!empty($area_code) && !empty($fax)) {$fax = "$area_code $fax";}

           
            
            
            $address = array(
                  //'source' => 4, //http://drupal.org/node/1041632 Set Location source 4 (or run UPDATE location SET source = 4) for GeoCoding of latitude and longitude
                  'name' => $data[1],
                  'street' => $data[2],
                  'additional' => $additional,
                  'city' => $data[5],
                  'province' => $state,
                  'postal_code' => $data[7],
          //the following fields are handled by extensions to the location 
                  'email' => $data[14],
                  'phone' => implode(', ', array_filter($phone)), //implode if value is not null,  the array_filter will skip the empty values by returning false.
                  'fax' => $fax,
                  'website' => $data[15],
            );

           
              $country_code = $data[8];//strtolower($data[8]);
               if($country_code=='UK')
                {
                    $country_code='GB';
                }
            // Check if $country_code exists in array of countries in the standard library.
                  
            $country_list = CountryManager::getStandardList();
            if (array_key_exists($country_code, $country_list)){
              $address['country'] = $country_list[$country_code];
            }
            else{
              $context['results']['messages'][] = "ALERT: Could not find country_code for country '$country_code'";
            }
            
            $address_output = "<div class='adr'>"
                    . "<span>{$address['name']}</span>"
                    . "<div class='street-address'>{$address['street']}, {$address['additional']}</div>"
                    . "<div class='street-address'>{$address['city']}, {$address['province']}</div>"
                    . "<div class='street-address'>{$address['country']}</div>";
                    
            if(!empty($address['email'])){
            $address_output .= "<div class='email'><lable>Email:<a href='mailto:{$address['email']}'>{$address['email']}</a></div>";
            }
            $address_output .= "<div class='phone'><lable>Phone:{$address['phone']}</div>"
                     . "</div>";
            
            
            return $address_output;
    }
    
    
}