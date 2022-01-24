<?php use My_module\Controller;


if (!defined('_PS_VERSION_')) {
    exit;
}

class my_module extends Module
{
    public function __construct()
    {
        $this->name = 'my_module'; 
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'dva'; 
        $this->need_instance = 1; 
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
        $this->bootstrap = true; 
        parent::__construct();
        $this->displayName = $this->l('my_module'); 
        $this->description = $this->l('description');
        $this->confirmUninstall = $this->l('Are you serious?');

    }
    public function install()
    {
         if (Shop::isFeatureActive())
        {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        Configuration::updateValue('MYMODULEFROM', null);
        Configuration::updateValue('MYMODULEUNTIL', null);

        if (!parent::install() || !$this->registerHook('displayFooter')) {
            return false;
        }

        return true;

    }

    public function uninstall()
    {
        Configuration::deleteByName('MYMODULEFROM', null);
        Configuration::deleteByName('MYMODULEUNTIL', null);

        return true;
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {

            $from = (string) Tools::getValue('MYMODULEFROM');
            $until = (string) Tools::getValue('MYMODULEUNTIL');

            if (empty($from) || !Validate::isGenericName($from)) {
                // invalid value, show an error
                $output = $this->displayError($this->l('Invalid Configuration value'));
            } else {
                Configuration::updateValue('MYMODULEFROM', $from);
                Configuration::updateValue('MYMODULEUNTIL', $until);
                $output = $this->displayConfirmation($this->l('Settings updated'));
            }
    

            
        }

        return $output . $this->displayForm();
    }

    public function displayForm()
{
    // Init Fields form array
    $form = [
        'form' => [
            'legend' => [
                'title' => $this->l('Settings'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Configuration value from'),
                    'name' => 'MYMODULEFROM',
                    'size' => 3,
                    'required' => true,
                ],[
                    'type' => 'text',
                    'label' => $this->l('Configuration value until'),
                    'name' => 'MYMODULEUNTIL',
                    'size' => 3,
                    'required' => true,
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
            ],
        ],
    ];

    $helper = new HelperForm();

    // Module, token and currentIndex
    $helper->table = $this->table;
    $helper->name_controller = $this->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
    $helper->submit_action = 'submit' . $this->name;

    // Default language
    $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

    // Load current value into the form
    $helper->fields_value['MYMODULE_CONFIG'] = Tools::getValue('MYMODULE_CONFIG', Configuration::get('MYMODULE_CONFIG'));

    return $helper->generateForm([$form]);
}

    public function hookDisplayFooter($params){
        
        $from = Configuration::get('MYMODULEFROM') ? (string) Configuration::get('MYMODULEFROM') : '0';
        $until = Configuration::get('MYMODULEUNTIL') ? (string) Configuration::get('MYMODULEUNTIL') : '0';        
        $notice = '';
        $products = Db::getInstance()->ExecuteS(sprintf('SELECT * FROM `'._DB_PREFIX_.'product` WHERE price > %s AND price < %s', $from, $until));

        if(!$products){ $notice = "Что-то пошло не по плану";}

        $this->context->smarty->assign([
            'countProducts' => count($products),
            'products' => $products,
            'notice' => $notice
        ]);
        return $this->display(__FILE__, 'my_module.tpl');
    }

    
}
