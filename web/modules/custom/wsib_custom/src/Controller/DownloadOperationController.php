<?php

namespace Drupal\wsib_custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\HttpFoundation\Response;
use setasign\Fpdi\Fpdi;

/**
 * Controller for the custom operation.
 */
class DownloadOperationController extends ControllerBase {

  public function downloadOperation(EntityInterface $node) {

    $firstname = $node->get('field_wsib_first_name')[0]->getValue()['value'];
    $lastname = $node->get('field_wsib_last_name')[0]->getValue()['value'];
    $occupations = $node->get('field_wsib_job_title')->referencedEntities();
    $return_to_works = $node->get('field_return_to_work')->referencedEntities();

    // Loop through each referenced taxonomy entity.
    foreach ($occupations as $occupation) {
      $jobname = $occupation->getName();
    }

    // Get the referenced entities from the 'field_lost_time_no_lost_time' field.
    $field_lost_time_no_lost_timep_aragraphs = $node->get('field_lost_time_no_lost_time')->referencedEntities();

    // Loop through each referenced paragraph entity.
    foreach ($field_lost_time_no_lost_timep_aragraphs as $paragraph) {
        // Now you can access the fields within each paragraph entity.
        // For example, to get the value of a field named 'field_example':
        if ($paragraph->get('field_wsib_other_name')->getValue()) {
          $field_wsib_other_name = $paragraph->get('field_wsib_other_name')->getValue()[0]['value'];
        }
        if ($paragraph->get('field_wsib_other_telephone')->getValue()) {
          $field_wsib_other_telephone = $paragraph->get('field_wsib_other_telephone')->getValue()[0]['value'];
        }
        if ($paragraph->get('field_wsib_return_to_work')->getValue()[0]['value']) {
          $field_wsib_return_to_work = $paragraph->get('field_wsib_return_to_work')->getValue()[0]['value'];
        }
        if ($paragraph->get('field_date_worker_lost')->getValue()[0]['value']) {
          $field_date_worker_lost = $paragraph->get('field_date_worker_lost')->getValue()[0]['value'];
        }
        if ($paragraph->get('field_date_returned_to_work')->getValue()[0]['value']) {
          $field_date_returned_to_work = $paragraph->get('field_date_returned_to_work')->getValue()[0]['value'];
        }
        if ($paragraph->get('field_modified_work_confirmed_by')->getValue()[0]['value']) {
          $field_modified_work_confirmed_by = $paragraph->get('field_modified_work_confirmed_by')->getValue()[0]['value'];
        }

    }

    // Loop through each referenced paragraph entity.
    foreach ($return_to_works as $return_to_work) {
        // Now you can access the fields within each paragraph entity.
        // For example, to get the value of a field named 'field_example':
        if ($return_to_work->get('field_wsib_other_name')->getValue()[0]['value']) {
          $field_wsib_other_name_2 = $return_to_work->get('field_wsib_other_name')->getValue()[0]['value'];
        }
        if ($return_to_work->get('field_wsib_other_telephone')->getValue()[0]['value']) {
          $field_wsib_other_telephone_2 = $return_to_work->get('field_wsib_other_telephone')->getValue()[0]['value'];
        }
        if ($return_to_work->get('field_responsible_for_arranging')->getValue()[0]['value']) {
          $field_responsible_for_arranging = $return_to_work->get('field_responsible_for_arranging')->getValue()[0]['value'];
        }
        if ($return_to_work->get('field_wsib_offere_result')->getValue()) {
          $field_wsib_offere_result = $return_to_work->get('field_wsib_offere_result')->getValue()[0]['value'];
        }
        if ($return_to_work->get('field_wsib_work_limitations')->getValue()[0]['value']) {
          $field_wsib_work_limitations = $return_to_work->get('field_wsib_work_limitations')->getValue()[0]['value'];
        }
        if ($return_to_work->get('field_wsib_modified_work_offer')->getValue()[0]['value']) {
          $field_wsib_modified_work_offer = $return_to_work->get('field_wsib_modified_work_offer')->getValue()[0]['value'];
        }
        if ($return_to_work->get('field_wsib_modified_work')->getValue()[0]['value']) {
          $field_wsib_modified_work = $return_to_work->get('field_wsib_modified_work')->getValue()[0]['value'];
        }

    }

    // Path to the fillable PDF form.
    $pdfPath = 'modules/custom/wsib_custom/files/new_test.pdf';

    // Create an instance of FPDI.
    $pdf = new Fpdi();

    // Get the total number of pages in the PDF.
    $totalPages = $pdf->setSourceFile($pdfPath);

    $pdf->SetFont('Arial');
    $pdf->SetFontSize(12);

    // Loop through all pages and import them.
    for ($pageNumber = 1; $pageNumber <= 1; $pageNumber++) {
      // Add a new page in the destination PDF.
      $pdf->AddPage();

      // Import the current page of the existing PDF.
      $templateId = $pdf->importPage($pageNumber);

      // Use the imported page as a template.
      $pdf->useTemplate($templateId);
      $pdf->SetXY(14, 54);
      $pdf->Write(0, $lastname);

      $pdf->SetXY(63, 54);
      $pdf->Write(0, $firstname);

      $pdf->SetXY(8.2, 36);
      $pdf->Write(0, $jobname);
    }

    // Loop through all pages and import them.
    for ($pageNumber = 2; $pageNumber <= 2; $pageNumber++) {
      // Add a new page in the destination PDF.
      $pdf->AddPage();

      // Import the current page of the existing PDF.
      $templateId = $pdf->importPage($pageNumber);

      // Use the imported page as a template.
      $pdf->useTemplate($templateId);

      // Path to the image file you want to place on the PDF.
      $imagePath = 'modules/custom/wsib_custom/images/checkmark.png';
      if ($field_wsib_return_to_work == "has_lost_time") {
        //$pdf->SetXY(8.2, 36);
        $pdf->Image($imagePath, 9.6, 204, 5, 5);
        $date = strtotime($field_date_worker_lost);
        // Convert the date into the desired format.
        $day = date('d', $date); // Day (01-31)
        $month = date('m', $date); // Month (01-12)
        $year = date('y', $date); // Year (two digits)
        $pdf->SetXY(66, 219);
        $pdf->Write(0, $day);
        $pdf->SetXY(76, 219);
        $pdf->Write(0, $month);
        $pdf->SetXY(84, 219);
        $pdf->Write(0, $year);
        $date2 = strtotime($field_date_returned_to_work);
        // Convert the date into the desired format.
        $day2 = date('d', $date2); // Day (01-31)
        $month2 = date('m', $date2); // Month (01-12)
        $year2 = date('y', $date2); // Year (two digits)
        $pdf->SetXY(150, 219);
        $pdf->Write(0, $day2);
        $pdf->SetXY(160, 219);
        $pdf->Write(0, $month2);
        $pdf->SetXY(168, 219);
        $pdf->Write(0, $year2);
      }
      if ($field_wsib_return_to_work == "modified_work_not_lost") {
        $pdf->Image($imagePath, 9.6, 199, 5, 5);
      }
      if ($field_wsib_return_to_work == "regular_job_has_not_lost") {
        $pdf->Image($imagePath, 9.6, 195, 5, 5);
      }
      if ($field_modified_work_confirmed_by == "myself") {
        $pdf->Image($imagePath, 12.3, 225, 5, 5);
      }
      if ($field_modified_work_confirmed_by == "other") {
        $pdf->Image($imagePath, 40, 225, 5, 5);
        $pdf->SetXY(58, 230);
        $pdf->Write(0, $field_wsib_other_name);
        $pdf->SetXY(136, 230);
        $pdf->Write(0, $field_wsib_other_telephone);
      }
      if ($field_wsib_work_limitations == "yes") {
        $pdf->Image($imagePath, 32, 249, 5, 5);
      }
      if ($field_wsib_work_limitations == "no") {
        $pdf->Image($imagePath, 42, 249, 5, 5);
      }
      if ($field_wsib_modified_work_offer == "yes") {
        $pdf->Image($imagePath, 72, 249, 5, 5);
      }
      if ($field_wsib_modified_work_offer == "no") {
        $pdf->Image($imagePath, 82, 249, 5, 5);
      }
      if ($field_wsib_modified_work_offer == "yes") {
        $pdf->Image($imagePath, 118.5, 249, 5, 5);
      }
      if ($field_wsib_modified_work_offer == "no") {
        $pdf->Image($imagePath, 128.5, 249, 5, 5);
      }
      if ($field_wsib_offere_result == "accepted") {
        $pdf->Image($imagePath, 166, 240.5, 5, 5);
      }
      if ($field_wsib_offere_result == "declined") {
        $pdf->Image($imagePath, 184.5, 240.5, 5, 5);
      }
      if ($field_responsible_for_arranging == "myself") {
        $pdf->Image($imagePath, 12.3, 259.6, 5, 5);
      }
      if ($field_responsible_for_arranging == "other") {
        $pdf->Image($imagePath, 40, 259.6, 5, 5);
        $pdf->SetXY(58, 266);
        $pdf->Write(0, $field_wsib_other_name_2);
        $pdf->SetXY(136, 266);
        $pdf->Write(0, $field_wsib_other_telephone_2);
      }
    }

    // Loop through all pages and import them.
    for ($pageNumber = 3; $pageNumber <= 4; $pageNumber++) {
      // Add a new page in the destination PDF.
      $pdf->AddPage();

      // Import the current page of the existing PDF.
      $templateId = $pdf->importPage($pageNumber);

      // Use the imported page as a template.
      $pdf->useTemplate($templateId);
    }

    // Output the filled PDF to the browser.
    $pdf->Output('D', 'filled_form.pdf'); // 'D' means download
  }
}
