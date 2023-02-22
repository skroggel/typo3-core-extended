<?php

namespace Madj2k\CoreExtended\Error;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Crypto\Random;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;
use \TYPO3\CMS\Frontend\ContentObject\Exception\ProductionExceptionHandler;

/**
 * ProductionExceptionHandler
 * Exception handler class for content object rendering
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ContentObjectProductionExceptionHandler extends ProductionExceptionHandler
{

    /**
     * Handles exceptions thrown during rendering of content objects
     * The handler can decide whether to re-throw the exception or
     * return a nice error message for production context.
     *
     * @param \Exception $exception
     * @param AbstractContentObject|null $contentObject
     * @param array $contentObjectConfiguration
     * @return string
     * @throws \Exception
     */
    public function handle(
        \Exception $exception,
        AbstractContentObject $contentObject = null,
        $contentObjectConfiguration = []
    ) : string {

        if (!empty($this->configuration['ignoreCodes.'])) {
            if (in_array($exception->getCode(), array_map('intval', $this->configuration['ignoreCodes.']), true)) {
                throw $exception;
            }
        }

        $requestedUrl = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
        $errorMessage = $this->configuration['errorMessage'] ?? 'Oops, an error occurred! Code: %s';

        $random = GeneralUtility::makeInstance(Random::class);
        $code = date('YmdHis', $_SERVER['REQUEST_TIME']) . $random->generateRandomHexString(8);

        $this->logException($exception, $requestedUrl, $code);
        return str_replace('%s', $code, $errorMessage);
    }


    /**
     * @param \Exception $exception
     * @param string $errorMessage
     * @param string $code
     */
    protected function logException(\Exception $exception, $errorMessage, $code)
    {
        $this->logger->alert('Code "' . $code . '" on ' . $errorMessage, ['exception' => $exception]);
    }

}
