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

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;


class MdevRecaptcha extends Module implements WidgetInterface
{
    public function __construct()
    {
        $this->name = 'mdevrecaptcha';
        $this->tab = 'front_office_features';
        $this->author = 'MaluDev';
        $this->version = '1.1.5';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Google Re-Captcha v3 - Anti Spam');
        $this->description = $this->l('Implements latest Captcha by Google to: Login, Registration and Contact Form');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->ps_versions_compliancy = array('min'=>'1.7.0.0', 'max'=>'1.7.99.999');
        $this->module_key = '032bc1cedede60a8f290504398dde8ec';
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('additionalCustomerFormFields')
            && $this->registerHook('validateCustomerFormFields')
            && $this->registerHook('actionFrontControllerSetMedia')
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayGDPRConsent')
            && $this->registerHook('postCommentValidation')
        ;
    }

    public function uninstall()
    {
        $this->removeConfig();
        return parent::uninstall();
    }


    // WIDGET FUNCTIONS
    public function renderWidget($hookName, array $configuration)
    {
        $this->smarty->assign($this->getWidgetVariables($hookName , $configuration));

        return $this->fetch('module:'.$this->name.'/views/templates/widget/mdevrecaptcha.tpl');
    }

    public function getWidgetVariables($hookName , array $configuration)
    {
        return [
            'recaptcha_token' => Configuration::get('MDEVRECAPTCHA_WEBKEY')
        ];
    }
    // END WIDGET


    //HOOKS
    public function hookAdditionalCustomerFormFields()
    {
        if ($this->isValidSection()) {
            $captchaField = $this->getCaptchaField();
            return [$captchaField];
        }
    }

    public function hookValidateCustomerFormFields($params)
    {
        if ($this->isValidSection()) {
            foreach ($params['fields'] as $field) {
                if ($field->getName() == 'mdevrecaptcha_gtoken') {
                    if (!$this->validToken($field->getValue())) {
                        $field->addError($this->getCaptchaViolationMessage());
                    }
                }
            }
        }
    }

    public function hookActionFrontControllerSetMedia($params)
    {
        if ($this->isValidSection()) {
            $this->context->controller->registerJavascript(
                'sj-rcg-front-js',
                'https://www.google.com/recaptcha/api.js?render='. Configuration::get('MDEVRECAPTCHA_WEBKEY'),
                ['server' => 'remote']
            );

            $this->context->controller->registerJavascript(
                'sj-rc-front-js',
                'modules/' . $this->name . '/views/js/front.js'
            );
        }
    }

    public function hookDisplayHeader()
    {
        if (!$this->isValidSection()) {
            return "";
        }

        $siteKey = Configuration::get('MDEVRECAPTCHA_WEBKEY');
        $this->context->smarty->assign(array('siteKey' => $siteKey));

        return $this->display(__FILE__, 'views/templates/front/script_key.tpl');
    }

    public function hookDisplayGDPRConsent($params)
    {
        if (!$this->isValidSection()) {
            return "";
        }

        return $this->getCaptchaFieldHtml();
    }

    public function hookPostCommentValidation($params)
    {
        if( !isset($params['fields']) || !is_array($params['fields']) ) return false;
        if( !isset($params['fields']['mdevrecaptcha_gtoken']) ) return false;

        if (!$this->isValidSection('postcomment')) {
            return false;
        }

        return $this->validToken( $params['fields']['mdevrecaptcha_gtoken'] );
    }
    //END HOOKS

    //Hooks Functions
    public function validToken($gtoken)
    {
        if (!$gtoken || $gtoken == "") {
            return false;
        } else {
            $googleRequest = $this->postApi($gtoken);

            if ($googleRequest && isset($googleRequest->success)) {
                if (!$googleRequest->success) {
                    return false;
                }

                return true;
            } else {
                return false;
            }
        }
    }

    public function getCaptchaField()
    {
        return (new FormField())
            ->setName('mdevrecaptcha_gtoken')
            ->setType('hidden')
            ->setRequired(true)
            ->addAvailableValue(Configuration::get('MDEVRECAPTCHA_WEBKEY'), 'data-client')
        ;
    }

    private function getCaptchaFieldHtml()
    {
        $captchaField = $this->getCaptchaField()->toArray();
        $this->context->smarty->assign(array( 'captchaField' => $captchaField ));

        return $this->display(__FILE__, 'views/templates/hook/hookDisplayGDPRConsent.tpl');
    }

    public function isValidSection($sectionName = '')
    {
        $siteKey = Configuration::get('MDEVRECAPTCHA_WEBKEY');
        $secretKey = Configuration::get('MDEVRECAPTCHA_SECRETKEY');

        // Check if Captcha is configure
        if (!$siteKey || !$secretKey) {
            return false;
        }

        $section = $sectionName ? $sectionName : $this->context->controller->php_self;
        $availableSections = Configuration::get('MDEVRECAPTCHA_SECTIONS');

        return (strrpos($availableSections, 'login') !== false && $section == "authentication" && Tools::getValue("create_account") != 1) //Login
                || (strrpos($availableSections, 'registration') !== false && $section == "authentication" && Tools::getValue("create_account") == 1) //Resgitration
                || (strrpos($availableSections, 'contact') !== false && $section == "contact") //Contact Form
                || (strrpos($availableSections, 'postcomment') !== false && $section == "postcomment") //Post Comment Form
        ;
    }
    //End Hooks Functions


    //Configuracion del módulo
    public function getContent()
    {
        $is_saved = false;

        if (Tools::isSubmit('save')) {
            $webkey = Tools::getValue('webkey');
            $secretkey = Tools::getValue('secretkey');

            $arraySections = array(
                Tools::getValue('use_in_login'),
                Tools::getValue('use_in_registration'),
                Tools::getValue('use_in_contact'),
                Tools::getValue('use_in_postcomment')
            );

            Configuration::updateValue('MDEVRECAPTCHA_WEBKEY', $webkey);
            Configuration::updateValue('MDEVRECAPTCHA_SECRETKEY', $secretkey);
            $this->saveConfigSections($arraySections);

            $is_saved = true;
        }

        $actualSections = Configuration::get('MDEVRECAPTCHA_SECTIONS');

        $this->context->smarty->assign(array(
            'webkey' => Configuration::get('MDEVRECAPTCHA_WEBKEY'),
            'secretkey' => Configuration::get('MDEVRECAPTCHA_SECRETKEY'),
            'useLogin' => strrpos($actualSections, 'login') !== false,
            'useRegistration' => strrpos($actualSections, 'registration') !== false,
            'useContact' => strrpos($actualSections, 'contact') !== false,
            'usePostcomment' => strrpos($actualSections, 'postcomment') !== false,
            'is_saved' => $is_saved,
        ));

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }
    //Fin Configuracion del módulo


    public function getCaptchaViolationMessage()
    {
        return $this->l('There was an error while verifying the captcha.');
    }


    private function postApi($token)
    {
        $url = 'https://www.google.com/recaptcha/api/siteverify';

        $data = array(
            'secret' => Configuration::get('MDEVRECAPTCHA_SECRETKEY'),
            'response' => $token
        );

        $response = Tools::file_get_contents(
            $url . "?secret=" . $data["secret"] . "&response=" . $data["response"]
        );

        return json_decode($response);
    }


    private function saveConfigSections($arraySections)
    {
        $result = "";

        foreach ($arraySections as $value) {
            if ($value) {
                $result .= $result == "" ? $value : "," . $value;
            }
        }

        Configuration::updateValue('MDEVRECAPTCHA_SECTIONS', $result);
    }


    private function removeConfig()
    {
        Configuration::deleteByName('MDEVRECAPTCHA_WEBKEY');
        Configuration::deleteByName('MDEVRECAPTCHA_SECRETKEY');
        Configuration::deleteByName('MDEVRECAPTCHA_SECTIONS');
    }
}
