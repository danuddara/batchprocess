<?php

use \Drupal\node\Entity\Node;
use \Drupal\trutest_distributor_dealer\Controller;

define('TRUTEST_DEALERS_IMPORT_DELIMITER_DEFAULT', chr(9));
/**
 * Handle batch completion.
 *
 *   Creates a new CSV file containing all failed rows if any.
 */
function csvimport_import_finished($success, $results, $operations) {

 
  if($success==true)
  {
     $category = $results['import_category']; 
     $rowscreated = $results['new_imported'];
     $rowsdeleted = $results['deleted_imported'];
     $rowsupdated = $results['updated_imported'];
     
     drupal_set_message(t('The @category Import is completed. created : @rowscreated , Updated : @rowsupdated , Deleted : @rowsdeleted ,  ',
                          [
                           '@category'=>$category,
                           '@rowscreated'=>$rowscreated,
                           '@rowsupdated'=>$rowsupdated,
                           '@rowsdeleted'=>$rowsdeleted,   
                          ]));
     
     
  }
  else
  {
       drupal_set_message(t('Error occoured on import'),'error');
  }
  
  
  if(!empty($context['results']['messages']))
  {
      foreach($context['results']['messages'] as $message)
      {
          drupal_set_message(t($message),'warning');
      }
      
  }
  
  //dsm($success);
 // dsm($results);
 // dsm($operations);

  return t('The CSV import has completed.');
}

/**
 * Remember the uploaded CSV filename.
 *
 * @TODO Is there a better way to pass a value from inception of the batch to
 * the finished function?
 */
function _csvimport_remember_init($filename, $import_category, &$context) {

  $context['results']['uploaded_filename'] = $filename;
  $context['results']['import_category'] = $import_category;
  $context['results']['rows_imported']=0;
  $context['results']['new_imported']=0;
  $context['results']['deleted_imported']=0;
  $context['results']['updated_imported']=0;
}

/**
 * Process a single line.
 */
function _csvimport_import_line($line,$brand_col,$category, &$context) {

  $context['results']['rows_imported']++;
  $line = $cleaned_line = array_map('base64_decode', $line);

  if(!empty($line[$brand_col])){

    /**
     * Simply show the import row count.
     */
   // $context['message'] = t('Importing row @c', ['@c' => $context['results']['rows_imported']]);

    /**
     * Alternatively, our example CSV happens to have the title in the
     * third column, so we can uncomment this line to display "Importing
     * Blahblah" as each row is parsed.
     *
     * You can comment out the line above if you uncomment this one.
     */
     $context['message'] = t('Importing @title', ['@title' => $line[0]]);

    /**
     * In order to slow importing and debug better, we can uncomment
     * this line to make each import slightly slower.
     */
    usleep(2500);

    /**
     * Convert the line of the CSV file into a new node.
     *
     */
    // if ($context['results']['rows_imported'] > 1) { // Skip header line.
    //   $node = Node::create([
    //     'type'  => 'article',
    //     'title' => $line[2],
    //     'body'  => $line[0],
    //   ]);
    //
    //   $node->save();
    // }
    
    /*Based on the category we create the nodes needed*/
    if($category=='Dealer')
    {
             
        $dealer_import = new Controller\DealerImportController();
        $dealer_import->importDealers($line, $context);
      //  DealerImportController::importDealers($line,$context);
       // dpm($node);
        
    }
    else if($category=='Distributor')
    {
        $distributor_import = new Controller\DistributorImportController();
        $distributor_import->importDistributors($line, $context);
        
    }
    

  }

}
