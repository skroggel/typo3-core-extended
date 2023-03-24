<?php
namespace Madj2k\CoreExtended\ExpressionLanguage\Functions;

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

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * BackendLayoutFunctionsProvider
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class BackendLayoutFunctionsProvider implements ExpressionFunctionProviderInterface
{

    /**
     * @return ExpressionFunction[]
     */
    public function getFunctions(): array
    {
        return [
            $this->getBackendLayoutFunction(),
        ];
    }


    /**
     * @return ExpressionFunction
     */
    protected function getBackendLayoutFunction(): ExpressionFunction
    {
        return new ExpressionFunction('backendLayout', function () {
            // Not implemented, we only use the evaluator
        }, function ($existingVariables) {

            $colPos = GeneralUtility::_GP('colPos'); // no intval here; must be able to be false

            // get pid from params or from element
            $pid = $this->getPid();

            // do not use this on gridElements
            // those have colPos = -1
            if ($colPos < 0){
                return '';
            }

            // check backendLayout
            if ($pid){

                // check backend layout of page
                $currentPage = BackendUtility::getRecord('pages', $pid, 'backend_layout');
                if ($currentPage['backend_layout']) {
                   return $currentPage['backend_layout'];
                }

                // get rootline
                $rootline = BackendUtility::BEgetRootLine($pid);
                if ($count = (count($rootline) -1)) {

                    foreach ($rootline as $iterator => $page) {

                        // do not check the current page itself
                        if ($iterator == $count) {
                            continue;
                        }

                        if ($page['backend_layout_next_level']) {
                            return $page['backend_layout_next_level'];
                        }
                    }
                }
            }

            return '';

        });
    }


    /**
     * Returns current PID
     *
     * @return int
     */
    public function getPid (): int
    {
        $pid = intval(GeneralUtility::_GP('id'));
        if ($editArray = GeneralUtility::_GP('edit')) {
            if (
                ($table = array_key_first($editArray))
                && ($table == 'tt_content')
            ){

                if (
                    ($uid = array_key_first($editArray[$table]))
                    && ($action = $editArray[$table][$uid])
                ) {
                    if ($action != 'new') {

                        if ($record = BackendUtility::getRecord($table, $uid, 'pid')) {
                            $pid = $record['pid'];
                        }

                        /*if ($table == 'tt_content') {
                            $colPos = BackendUtility::getRecord($table, $uid, 'colPos')['colPos'];
                        }*/
                    }
                }
            }
        }

        return $pid;
    }
}
