<?php
namespace Madj2k\CoreExtended\XClasses\Frontend\Authentication;

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

use Symfony\Component\HttpFoundation\Cookie;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FrontendUserAuthentication
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FrontendUserAuthentication extends \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
{

    /**
     * @var string
     */
    protected string $feLoginCookieName = 'fe_logged_in';


    /**
     * Sets the session cookie for the current disposal.
     *
     * @return void
     * @throws Exception
     */
    protected function setSessionCookie(): void
    {
        parent::setSessionCookie();

        /**
         * We set an additional cookie which allows us to check
         * on JavaScript-basis if a user is logged in or not
         */
        $isSetSessionCookie = $this->isSetSessionCookie();
        $isRefreshTimeBasedCookie = $this->isRefreshTimeBasedCookie();
        if ($isSetSessionCookie || $isRefreshTimeBasedCookie) {

            $settings = $GLOBALS['TYPO3_CONF_VARS']['SYS'];
            // Get the domain to be used for the cookie (if any):
            $cookieDomain = $this->getCookieDomain();
            // If no cookie domain is set, use the base path:
            $cookiePath = $cookieDomain ? '/' : GeneralUtility::getIndpEnv('TYPO3_SITE_PATH');
            // If the cookie lifetime is set, use it:
            $cookieExpire = $isRefreshTimeBasedCookie ? $GLOBALS['EXEC_TIME'] + $this->lifetime : 0;
            // Use the secure option when the current request is served by a secure connection:
            $cookieSecure = (bool)$settings['cookieSecure'] && GeneralUtility::getIndpEnv('TYPO3_SSL');
            // Valid options are "strict", "lax" or "none", whereas "none" only works in HTTPS requests (default & fallback is "strict")
            $cookieSameSite = $this->sanitizeSameSiteCookieValue(
                strtolower($GLOBALS['TYPO3_CONF_VARS'][$this->loginType]['cookieSameSite'] ?? Cookie::SAMESITE_STRICT)
            );
            // SameSite "none" needs the secure option (only allowed on HTTPS)
            if ($cookieSameSite === Cookie::SAMESITE_NONE) {
                $cookieSecure = true;
            }
            // Do not set cookie if cookieSecure is set to "1" (force HTTPS) and no secure channel is used:
            if ((int)$settings['cookieSecure'] !== 1 || GeneralUtility::getIndpEnv('TYPO3_SSL')) {

                // check if a user is logged in!
                // since at this point there was no redirect, we can't check via context here!
                if (isset($this->user['uid'])) {
                    $cookie = new Cookie(
                        $this->feLoginCookieName,
                        '1',
                        $cookieExpire,
                        $cookiePath,
                        $cookieDomain,
                        $cookieSecure,
                        false, // HttpOnly = true prevents JavaScript from accessing the session cookie.
                        false,
                        $cookieSameSite
                    );
                    header('Set-Cookie: ' . $cookie->__toString(), false);
                }
            } else {
                throw new Exception('Cookie was not set since HTTPS was forced in $TYPO3_CONF_VARS[SYS][cookieSecure].', 1254325546);
            }
        }
    }


    /**
     * Empty / unset the cookie
     *
     * @param string $cookieName usually, this is $this->name
     * @return void
     */
    public function removeCookie($cookieName): void
    {
        parent::removeCookie($cookieName);

        /**
         * We remove the additional cookie which allows us to check
         * on JavaScript-basis if a user is logged in or not
         */
        $cookieDomain = $this->getCookieDomain();

        // If no cookie domain is set, use the base path
        $cookiePath = $cookieDomain ? '/' : GeneralUtility::getIndpEnv('TYPO3_SITE_PATH');
        setcookie($this->feLoginCookieName, null, -1, $cookiePath, $cookieDomain);
    }


    /**
     * @param string $cookieSameSite
     * @return string
     */
    private function sanitizeSameSiteCookieValue(string $cookieSameSite): string
    {
        if (!in_array($cookieSameSite, [Cookie::SAMESITE_STRICT, Cookie::SAMESITE_LAX, Cookie::SAMESITE_NONE], true)) {
            $cookieSameSite = Cookie::SAMESITE_STRICT;
        }
        return $cookieSameSite;
    }

}
