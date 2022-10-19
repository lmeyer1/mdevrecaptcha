<?php
/**
 * 2007-2020 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Sebastián Jaimovich
 *  @copyright Copyright (c) Sebastián Jaimovich
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ContactController extends ContactControllerCore
{
    public function initContent()
    {
        $mdevrecaptcha_module = Module::getInstanceByName('mdevrecaptcha');

        if ($mdevrecaptcha_module->isValidSection() && Tools::isSubmit('submitMessage')) {
            $gtoken = Tools::getValue("mdevrecaptcha_gtoken");

            if ($mdevrecaptcha_module->validToken($gtoken) === false) {
                $this->errors[] = $mdevrecaptcha_module->getCaptchaViolationMessage();
            }
        }

        parent::initContent();
    }
}
