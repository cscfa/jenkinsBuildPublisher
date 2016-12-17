<?php

namespace AppBundle\Parser;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class FormParser
{

    public function getErrorAsString(FormInterface $form)
    {
        $info = $this->parse($form->createView());

        $errors = $this->parseFormInfo($info);
        $arrayError = array();

        foreach ($errors as $field => $errorArray) {
            array_push($arrayError, $field . ': ' . implode(' | ', $errorArray));
        }

        return implode("\n", $arrayError);
    }

    private function parseFormInfo(array $fieldInfo, $precedence = '')
    {
        $formErrors = array();

        $currentPath = $precedence.(empty($precedence) ? '' : '.').$fieldInfo['field'];

        if (isset($fieldInfo['childs'])) {
            foreach ($fieldInfo['childs'] as $child) {
                $childElements = $this->parseFormInfo(
                    $child,
                    $currentPath
                );

                foreach ($childElements as $field => $errors) {
                    $formErrors[$field] = $errors;
                }
            }
        } else if (isset($fieldInfo['errors'])) {
            $errors = array();
            foreach ($fieldInfo['errors'] as $error) {
                array_push($errors, $error);
            }
            $formErrors[$currentPath] = $errors;
        }

        return $formErrors;
    }

    private function parse(FormView $formView)
    {
        $result = array(
            'field' => $formView->vars['name'],
            'is_valid' => $formView->vars['valid']
        );

        foreach ($formView as $child) {
            if (! isset($result['childs'])) {
                $result['childs'] = array();
            }
            array_push($result['childs'], $this->parse($child));
        }

        if ($formView->count() == 0) {
            $result['value'] = $formView->vars['value'];
        }
        if (! $formView->vars['valid']) {

            $formErrors = $formView->vars['errors'];
            foreach ($formErrors as $error) {
                if (! isset($result['errors'])) {
                    $result['errors'] = array();
                }
                array_push($result['errors'], $this->getErrorMessage($error));
            }
        }

        return $result;
    }

    private function getErrorMessage(FormError $error)
    {
        return $error->getMessage();
    }
}
