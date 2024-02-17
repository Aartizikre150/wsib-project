<?php

namespace Drupal\fillpdf\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a deletion confirmation form for a FillPdfForm.
 *
 * @internal
 */
class FillPdfFormDeleteForm extends FillPdfFormConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete %name?', ['%name' => $this->entity->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return $this->getEntity()->toUrl('canonical');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $fillpdf_form = $this->getEntity();
    $fillpdf_form->delete();

    $this->messenger()->addStatus($this->t('FillPDF form deleted.'));

    $form_state->setRedirect('fillpdf.forms_admin');
  }

}
