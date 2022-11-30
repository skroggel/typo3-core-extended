<?php
namespace Madj2k\CoreExtended\Validation\Validator;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use IsoCodes\Iban;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Validator for Iban strings.
 *
 * @api
 */
class IbanValidator extends AbstractValidator
{
    /**
     * Checks if the given value is a valid Iban string. If this is not
     * the case, the function adds an error.
     *
     * @param mixed $value The value that should be validated
     * @return void
     */
    public function isValid($value)
    {

        if (! Iban::validate(str_replace(' ', '', $value))) {

            $this->addError(
                $this->translateErrorMessage(
                    'validator.iban.notvalid',
                    'RkwBasics',
                    [
                        gettype($value),
                    ]
                ), 1238087674, [gettype($value)]);

        }

    }
}
