<?php

namespace Drupal\fillpdf\Plugin\PdfBackend;

use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\file\FileInterface;
use Drupal\fillpdf\FieldMapping\ImageFieldMapping;
use Drupal\fillpdf\FieldMapping\TextFieldMapping;
use Drupal\fillpdf\Plugin\PdfBackendBase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * LocalServer PdfBackend plugin.
 *
 * @PdfBackend(
 *   id = "local_server",
 *   label = @Translation("FillPDF LocalServer"),
 *   description = @Translation("Network-accessible, self-installed PDF API. You will need a VPS or dedicated server."),
 *   weight = 5
 * )
 */
class LocalServerPdfBackend extends PdfBackendBase implements ContainerFactoryPluginInterface {

  /**
   * The file system.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * The Guzzle http client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * Constructs a LocalServerPdfBackend plugin object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \GuzzleHttp\Client $http_client
   *   The Guzzle http client.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, Client $http_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function parseFile(FileInterface $template_file) {
    $pdf_content = file_get_contents($template_file->getFileUri());
    return $this->parseStream($pdf_content);
  }

  /**
   * {@inheritdoc}
   */
  public function parseStream($pdf_content) {
    $request = [
      'pdf' => base64_encode($pdf_content),
    ];

    $json = json_encode($request);

    $fields = [];

    try {
      $fields_response = $this->httpClient->post($this->configuration['local_service_endpoint'] . '/api/v1/parse', [
        'body' => $json,
        'headers' => ['Content-Type' => 'application/json'],
      ]);
    }
    catch (RequestException $request_exception) {
      if ($response = $request_exception->getResponse()) {
        \Drupal::messenger()->addError($this->t('Error %code. Reason: %reason.', [
          '%code' => $response->getStatusCode(),
          '%reason' => $response->getReasonPhrase(),
        ]));
      }
      else {
        \Drupal::messenger()->addError($this->t('Unknown error occurred parsing PDF.'));
      }
    }

    $fields = json_decode((string) $fields_response->getBody(), TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function mergeFile(FileInterface $template_file, array $field_mappings, array $context) {
    $pdf_content = file_get_contents($template_file->getFileUri());
    return $this->mergeStream($pdf_content, $field_mappings, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function mergeStream($pdf_content, array $field_mappings, array $context) {
    $flatten = $context['flatten'];

    $api_fields = [];
    foreach ($field_mappings as $key => $mapping) {
      $api_field = NULL;

      if ($mapping instanceof TextFieldMapping) {
        $api_field = [
          'type' => 'text',
          'data' => $mapping->getData(),
        ];
      }
      elseif ($mapping instanceof ImageFieldMapping) {
        $api_field = [
          'type' => 'image',
          'data' => base64_encode($mapping->getData()),
        ];

        if ($extension = $mapping->getExtension()) {
          $api_field['extension'] = $extension;
        }
      }

      if ($api_field) {
        $api_fields[$key] = $api_field;
      }
    }

    $request = [
      'pdf' => base64_encode($pdf_content),
      'flatten' => $flatten,
      'fields' => $api_fields,
    ];

    $json = json_encode($request);

    try {
      $response = $this->httpClient->post($this->configuration['local_service_endpoint'] . '/api/v1/merge', [
        'body' => $json,
        'headers' => ['Content-Type' => 'application/json'],
      ]);

      $decoded = json_decode((string) $response->getBody(), TRUE);
      return base64_decode($decoded['pdf']);
    }
    catch (RequestException $e) {
      watchdog_exception('fillpdf', $e);
      return NULL;
    }
  }

}
