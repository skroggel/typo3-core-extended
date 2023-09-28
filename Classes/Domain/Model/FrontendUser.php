<?php
namespace Madj2k\CoreExtended\Domain\Model;

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

/**
 * Class FrontendUser
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_CoreExtended
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class FrontendUser extends \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
{

    /**
     * @var int
     */
    protected int $crdate = 0;


    /**
     * @var int
     */
    protected int $tstamp = 0;


    /**
     * @var int
     */
    protected int $starttime = 0;


    /**
     * @var int
     */
    protected int $endtime = 0;


    /**
     * @var bool
     */
    protected bool $disable = false;


    /**
     * @var bool
     */
    protected bool $deleted = false;


    /**
     * @var string
     */
    protected $email = '';


    /**
     * @var string
     */
    protected string $captchaResponse = '';


    /**
     * Sets the crdate value
     *
     * @param int $crdate
     * @api
     */
    public function setCrdate(int $crdate): void
    {
        $this->crdate = $crdate;
    }


    /**
     * Returns the crdate value
     *
     * @return int
     * @api
     */
    public function getCrdate(): int
    {
        return $this->crdate;
    }


    /**
     * Sets the tstamp value
     *
     * @param int $tstamp
     * @api
     */
    public function setTstamp(int $tstamp): void
    {
        $this->tstamp = $tstamp;
    }


    /**
     * Returns the tstamp value
     *
     * @return int
     * @api
     */
    public function getTstamp(): int
    {
        return $this->tstamp;
    }


    /**
     * Sets the starttime value
     *
     * @param int $starttime
     * @api
     */
    public function setStarttime(int $starttime): void
    {
        $this->starttime = $starttime;
    }


    /**
     * Returns the starttime value
     *
     * @return int
     * @api
     */
    public function getStarttime(): int
    {
        return $this->starttime;
    }


    /**
     * Sets the endtime value
     *
     * @param int $endtime
     * @api
     */
    public function setEndtime(int $endtime): void
    {
        $this->endtime = $endtime;
    }


    /**
     * Returns the endtime value
     *
     * @return int
     * @api
     */
    public function getEndtime(): int
    {
        return $this->endtime;
    }


    /**
     * Sets the disable value
     *
     * @param bool $disable
     * @return void
     *
     */
    public function setDisable(bool $disable): void
    {
        $this->disable = $disable;
    }


    /**
     * Returns the disable value
     *
     * @return bool
     */
    public function getDisable(): bool
    {
        return $this->disable;
    }


    /**
     * Sets the deleted value
     *
     * @param bool $deleted
     * @return void
     *
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }


    /**
     * Returns the deleted value
     *
     * @return bool
     *
     */
    public function getDeleted(): bool
    {
        return $this->deleted;
    }


    /**
     * Sets the username value
     * ! Important: We need to lowercase it !
     *
     * @param string $username
     * @return void
     * @api
     */
    public function setUsername($username): void
    {
        $this->username = strtolower($username);
    }


    /**
     * Sets the email value
     * ! Important: We need to lowercase it !
     *
     * @param string $email
     * @return void
     * @api
     */
    public function setEmail($email): void
    {
        $this->email = strtolower($email);
    }


    /**
     * Returns the title
     *
     * @return string
     * @api
     */
    public function getTitle(): string
    {
        return $this->title;
    }


    /**
     * Sets the title
     *
     * @param string $title
     * @api
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }



    /**
     * Sets the firstName
     *
     * @param string $firstName
     * @api
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;

        if ($this->getLastName()) {
            $this->name = $this->getFirstName() . ' ' . $this->getLastName();
        } else {
            $this->name = $this->getFirstName();
        }
    }


    /**
     * Sets the lastName
     *
     * @param string $lastName
     * @api
     */
    public function setLastName($lastName): void
    {

        $this->lastName = $lastName;

        if ($this->getFirstName()) {
            $this->name = $this->getFirstName() . ' ' . $this->getLastName();
        } else {
            $this->name = $this->getLastName();
        }
    }


    /**
     * Sets the captchaResponse
     *
     * @param string $captchaResponse
     * @return void
     */
    public function setCaptchaResponse(string $captchaResponse): void
    {
        $this->captchaResponse = $captchaResponse;
    }


    /**
     * Getter for captchaResponse
     *
     * @return string
     */
    public function getCaptchaResponse(): string
    {
        return $this->captchaResponse;
    }

}
