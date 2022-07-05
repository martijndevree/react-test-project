<?php

namespace Drupal\graphql_composable\Plugin\GraphQL\SchemaExtension;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistryInterface;
use Drupal\graphql\GraphQL\Response\ResponseInterface;
use Drupal\graphql\Plugin\GraphQL\SchemaExtension\SdlSchemaExtensionPluginBase;
use Drupal\graphql_composable\GraphQL\Response\TrackResponse;

/**
 * @SchemaExtension(
 *   id = "composable_extension",
 *   name = "Composable Example extension",
 *   description = "A simple extension that adds node related fields.",
 *   schema = "composable"
 * )
 */
class ComposableSchemaExampleExtension extends SdlSchemaExtensionPluginBase {

  /**
   * {@inheritdoc}
   */
  public function registerResolvers(ResolverRegistryInterface $registry): void {
    $builder = new ResolverBuilder();

    $registry->addFieldResolver('Query', 'track',
      $builder->produce('entity_load')
        ->map('type', $builder->fromValue('node'))
        ->map('bundles', $builder->fromValue(['track']))
        ->map('id', $builder->fromArgument('id'))
    );

    // Create track mutation.
    $registry->addFieldResolver('Mutation', 'createTrack',
      $builder->produce('create_track')
        ->map('data', $builder->fromArgument('data'))
    );

    $registry->addFieldResolver('TrackResponse', 'track',
      $builder->callback(function (TrackResponse $response) {
        return $response->track();
      })
    );

    $registry->addFieldResolver('TrackResponse', 'errors',
      $builder->callback(function (TrackResponse $response) {
        return $response->getViolations();
      })
    );

    $registry->addFieldResolver('Track', 'id',
      $builder->produce('entity_id')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('Track', 'title',
      $builder->compose(
        $builder->produce('entity_label')
          ->map('entity', $builder->fromParent())
      )
    );

    $registry->addFieldResolver('Track', 'fieldLikes',
      $builder->compose(
        $builder->produce('entity_label')
          ->map('entity', $builder->fromParent())
      )
    );

    $registry->addFieldResolver('Track', 'author',
      $builder->compose(
        $builder->produce('entity_owner')
          ->map('entity', $builder->fromParent()),
        $builder->produce('entity_label')
          ->map('entity', $builder->fromParent())
      )
    );

    // Response type resolver.
    $registry->addTypeResolver('Response', [
      __CLASS__,
      'resolveResponse',
    ]);
  }

  /**
   * Resolves the response type.
   *
   * @param \Drupal\graphql\GraphQL\Response\ResponseInterface $response
   *   Response object.
   *
   * @return string
   *   Response type.
   *
   * @throws \Exception
   *   Invalid response type.
   */
  public static function resolveResponse(ResponseInterface $response): string {
    // Resolve content response.
    if ($response instanceof TrackResponse) {
      return 'TrackResponse';
    }
    throw new \Exception('Invalid response type.');
  }

}
