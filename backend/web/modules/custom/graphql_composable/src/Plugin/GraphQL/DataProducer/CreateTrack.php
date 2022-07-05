<?php

namespace Drupal\graphql_composable\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\graphql_composable\GraphQL\Response\TrackResponse;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Creates a new track entity.
 *
 * @DataProducer(
 *   id = "create_track",
 *   name = @Translation("Create Track"),
 *   description = @Translation("Creates a new track."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Track")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Track data")
 *     )
 *   }
 * )
 */
class CreateTrack extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user')
    );
  }

  /**
   * CreateTrack constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(array $configuration, string $plugin_id, array $plugin_definition, AccountInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
  }

  /**
   * Creates a Track.
   *
   * @param array $data
   *   The submitted values for the track.
   *
   * @return \Drupal\graphql_composable\GraphQL\Response\TrackResponse
   *   The newly created track.
   *
   * @throws \Exception
   */
  public function resolve(array $data) {
    $response = new TrackResponse();
    if ($this->currentUser->hasPermission("create track content")) {
      $values = [
        'type' => 'track',
        'title' => $data['title'],
        'likes' => $data['likes'],
      ];
      $node = Node::create($values);
      $node->save();
      $response->setTrack($node);
    }
    else {
      $response->addViolation(
        $this->t('You do not have permissions to create tracks.')
      );
    }
    return $response;
  }

}
