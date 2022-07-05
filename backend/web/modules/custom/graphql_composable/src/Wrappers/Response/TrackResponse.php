<?php

declare(strict_types = 1);

namespace Drupal\graphql_composable\Wrappers\Response;

use Drupal\Core\Entity\EntityInterface;
use Drupal\graphql\GraphQL\Response\Response;

/**
 * Type of response used when a track is returned.
 */
class TrackResponse extends Response {

  /**
   * The track to be served.
   *
   * @var \Drupal\Core\Entity\EntityInterface|null
   */
  protected $track;

  /**
   * Sets the content.
   *
   * @param \Drupal\Core\Entity\EntityInterface|null $track
   *   The track to be served.
   */
  public function setTrack(?EntityInterface $track): void {
    $this->track = $track;
  }

  /**
   * Gets the track to be served.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The track to be served.
   */
  public function track(): ?EntityInterface {
    return $this->track;
  }

}
