<?php
namespace Madj2k\CoreExtended\XClasses\SrFreecap\Session;

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

use SJBR\SrFreecap\Domain\Model\Word;
use Symfony\Component\HttpFoundation\Cookie;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('sr_freecap')) {

    /**
     * Class SessionStorage
     *
     * We store the values in a cookie instead of the frontendUser-session because
     * we have massive trouble with the session-data. This is less secure - but works
     *
     * @author Steffen Kroggel <developer@steffenkroggel.de>
     * @copyright Steffen Kroggel
     * @package Madj2k_CoreExtended
     * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
     */
    class SessionStorage extends \SJBR\SrFreecap\Domain\Session\SessionStorage
    {

        /**
         * Returns the object stored in the user's cookie
         *
         * @return \SJBR\SrFreecap\Domain\Model\Word
         */
        public function restoreFromSession():? Word
        {
            if (
                ($cookieValue = $_COOKIE[self::SESSIONNAMESPACE])
                && ($cookieArray = unserialize($cookieValue))
                && (is_array($cookieArray))
            ){
                $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

                /** @var \SJBR\SrFreecap\Domain\Model\Word $word */
                $word = $objectManager->get(Word::class);

                $word->setWordHash($cookieArray['hash']);
                $word->setWordCypher($cookieArray['cypher']);

                return $word;
            }

            return null;
        }


        /**
         * Writes an object into the user's cookie
         *
         * @param \SJBR\SrFreecap\Domain\Model\Word $object any serializable object to store into the session
         * @return self
         * @throws Exception
         */
        public function writeToSession($object): self
        {

            /** @see \TYPO3\CMS\Core\Authentication\AbstractUserAuthentication::setSessionCookie() */
            $settings = $GLOBALS['TYPO3_CONF_VARS']['SYS'];
            // Get the domain to be used for the cookie (if any):
            $cookieDomain = $this->getCookieDomain();
            // If no cookie domain is set, use the base path:
            $cookiePath = $cookieDomain ? '/' : GeneralUtility::getIndpEnv('TYPO3_SITE_PATH');
            // Use the secure option when the current request is served by a secure connection:
            $cookieSecure = (bool)$settings['cookieSecure'] && GeneralUtility::getIndpEnv('TYPO3_SSL');

            // Do not set cookie if cookieSecure is set to "1" (force HTTPS) and no secure channel is used:
            if ((int)$settings['cookieSecure'] !== 1 || GeneralUtility::getIndpEnv('TYPO3_SSL')) {

                $cookieArray = [
                    'hash' => $object->getWordHash(),
                    'cypher' => $object->getWordCypher()
                ];

                $cookie = new Cookie(
                    self::SESSIONNAMESPACE,
                    serialize($cookieArray),
                    time()+3600,
                    $cookiePath,
                    $cookieDomain,
                    $cookieSecure,
                    true,
                    false,
                    'strict'
                );
                header('Set-Cookie: ' . $cookie->__toString(), false);

            } else {
                throw new Exception('Cookie was not set since HTTPS was forced in $TYPO3_CONF_VARS[SYS][cookieSecure].', 1678290572);
            }

            return $this;
        }


        /**
         * Cleans up the session: removes the stored object from the PHP session
         *
         * @return self
         * @throws Exception
         */
        public function cleanUpSession(): self
        {
            /** @see \TYPO3\CMS\Core\Authentication\AbstractUserAuthentication::setSessionCookie() */

            $settings = $GLOBALS['TYPO3_CONF_VARS']['SYS'];
            // Get the domain to be used for the cookie (if any):
            $cookieDomain = $this->getCookieDomain();
            // If no cookie domain is set, use the base path:
            $cookiePath = $cookieDomain ? '/' : GeneralUtility::getIndpEnv('TYPO3_SITE_PATH');
            // Use the secure option when the current request is served by a secure connection:
            $cookieSecure = (bool)$settings['cookieSecure'] && GeneralUtility::getIndpEnv('TYPO3_SSL');

            // Do not set cookie if cookieSecure is set to "1" (force HTTPS) and no secure channel is used:
            if ((int)$settings['cookieSecure'] !== 1 || GeneralUtility::getIndpEnv('TYPO3_SSL')) {

                $cookie = new Cookie(
                    self::SESSIONNAMESPACE,
                    null,
                    -1,
                    $cookiePath,
                    $cookieDomain,
                    $cookieSecure,
                    true,
                    false,
                    'strict'
                );
                header('Set-Cookie: ' . $cookie->__toString(), false);

            } else {
                throw new Exception('Cookie was not set since HTTPS was forced in $TYPO3_CONF_VARS[SYS][cookieSecure].', 1678290572);
            }

            return $this;
        }


        /**
         * Gets the domain to be used on setting cookies.
         * The information is taken from the value in $GLOBALS['TYPO3_CONF_VARS']['SYS']['cookieDomain'].
         *
         * @return string The domain to be used on setting cookies
         * @see \TYPO3\CMS\Core\Authentication\AbstractUserAuthentication::getCookieDomain()
         */
        protected function getCookieDomain(): string
        {
            $result = '';
            $cookieDomain = $GLOBALS['TYPO3_CONF_VARS']['SYS']['cookieDomain'];
            // If a specific cookie domain is defined for a given TYPO3_MODE,
            // use that domain
            if (!empty($GLOBALS['TYPO3_CONF_VARS'][$this->loginType]['cookieDomain'])) {
                $cookieDomain = $GLOBALS['TYPO3_CONF_VARS'][$this->loginType]['cookieDomain'];
            }
            if ($cookieDomain) {
                if ($cookieDomain[0] === '/') {
                    $match = [];
                    $matchCnt = @preg_match($cookieDomain, GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY'), $match);
                    if ($matchCnt === false) {
                        $this->logger->critical('The regular expression for the cookie domain (' . $cookieDomain . ') contains errors. The session is not shared across sub-domains.');
                    } elseif ($matchCnt) {
                        $result = $match[0];
                    }
                } else {
                    $result = $cookieDomain;
                }
            }
            return $result;
        }


        /**
         * @param string $cookieSameSite
         * @return string
         * @see \TYPO3\CMS\Core\Authentication\AbstractUserAuthentication::sanitizeSameSiteCookieValue();
         */
        private function sanitizeSameSiteCookieValue(string $cookieSameSite): string
        {
            if (!in_array($cookieSameSite, [Cookie::SAMESITE_STRICT, Cookie::SAMESITE_LAX, Cookie::SAMESITE_NONE], true)) {
                $cookieSameSite = Cookie::SAMESITE_STRICT;
            }
            return $cookieSameSite;
        }
    }

} else {
    /**
     * Class SessionStorage
     *
     * @author Steffen Kroggel <developer@steffenkroggel.de>
     * @copyright Steffen Kroggel
     * @package Madj2k_CoreExtended
     * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
     */
    class SessionStorage
    {
        // empty class to avoid errors
    }
}
