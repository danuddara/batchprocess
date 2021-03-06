<?php

namespace Drupal\trutest_distributor_dealer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormBuilderInterface;

/**
 * Provides a 'country_select' Block.
 *
 * @Block(
 *   id = "countryselect_block",
 *   admin_label = @Translation("Country select block"),
 *   category = @Translation("DealerDistributor"),
 *
 * )
 */
class CountrySelectBlock extends BlockBase implements BlockPluginInterface, ContainerFactoryPluginInterface {


  /**
   * Formbulder variable.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formbuilder;

  /**
   * Constructor loading with DI.
   *
   * @param array $configuration
   *   Config.
   * @param string $plugin_id
   *   Pluginid.
   * @param mixed $plugin_definition
   *   Definition.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   Formbulder.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, FormBuilderInterface $form_builder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->formbuilder = $form_builder;

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
        $configuration,
        $plugin_id,
        $plugin_definition,
        $container->get('form_builder')
         );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'label_display' => FALSE,
    ];

  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $builtForm = $this->formbuilder->getForm('Drupal\trutest_distributor_dealer\Form\DistributorDealerCountrySelectForm');
   $renderArray['form'] = $builtForm;
//$renderArray['form']='hellow';
    return $renderArray;

  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form_new = parent::blockForm($form, $form_state);

    return $form_new;

  }

}
