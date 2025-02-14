<?php

/*
 * Isotope eCommerce for Contao Open Source CMS
 *
 * Copyright (C) 2009 - 2019 terminal42 gmbh & Isotope eCommerce Workgroup
 *
 * @link       https://isotopeecommerce.org
 * @license    https://opensource.org/licenses/lgpl-3.0.html
 */

\System::loadLanguageFile(\Isotope\Model\Group::getTable());

/**
 * Extend tl_user palettes
 */
\Haste\Dca\PaletteManipulator::create()
    ->addLegend('isotope_legend', 'account_legend', \Haste\Dca\PaletteManipulator::POSITION_BEFORE)
    ->addField('iso_modules', 'isotope_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->addField('iso_product_types', 'isotope_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->addField('iso_product_typep', 'isotope_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->addField('iso_payment_modules', 'isotope_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->addField('iso_payment_modulep', 'isotope_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->addField('iso_shipping_modules', 'isotope_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->addField('iso_shipping_modulep', 'isotope_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->addField('iso_tax_classes', 'isotope_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->addField('iso_tax_classp', 'isotope_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->addField('iso_tax_rates', 'isotope_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->addField('iso_tax_ratep', 'isotope_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->addField('iso_configs', 'isotope_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->addField('iso_configp', 'isotope_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->addField('iso_groups', 'isotope_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->addField('iso_groupp', 'isotope_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->addField('iso_member_groups', 'isotope_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('extend', 'tl_user')
    ->applyToPalette('custom', 'tl_user')
;

/**
 * Add fields to tl_user
 */
$GLOBALS['TL_DCA']['tl_user']['fields']['iso_modules'] = array
(
    'label'                 => &$GLOBALS['TL_LANG']['tl_user']['iso_modules'],
    'exclude'               => true,
    'filter'                => true,
    'inputType'             => 'checkbox',
    'options_callback'      => array('Isotope\Backend', 'getIsotopeModules'),
    'reference'             => &$GLOBALS['TL_LANG']['IMD'],
    'eval'                  => array('multiple'=>true, 'helpwizard'=>true, 'tl_class'=>'clr w50 w50h'),
    'sql'                   => 'blob NULL',
);

$GLOBALS['TL_DCA']['tl_user']['fields']['iso_product_types'] = array
(
    'label'                 => &$GLOBALS['TL_LANG']['tl_user']['iso_product_types'],
    'exclude'               => true,
    'inputType'             => 'checkbox',
    'foreignKey'            => \Isotope\Model\ProductType::getTable().'.name',
    'eval'                  => array('multiple'=>true, 'tl_class'=>'clr w50 w50h'),
    'sql'                   => 'blob NULL',
);

$GLOBALS['TL_DCA']['tl_user']['fields']['iso_product_typep'] = array
(
    'label'                 => &$GLOBALS['TL_LANG']['tl_user']['iso_product_typep'],
    'exclude'               => true,
    'inputType'             => 'checkbox',
    'options'               => array('create', 'delete'),
    'reference'             => &$GLOBALS['TL_LANG']['MSC'],
    'eval'                  => array('multiple'=>true, 'tl_class'=>'w50 w50h'),
    'sql'                   => 'blob NULL',
);

$GLOBALS['TL_DCA']['tl_user']['fields']['iso_payment_modules'] = array
(
    'label'                 => &$GLOBALS['TL_LANG']['tl_user']['iso_payment_modules'],
    'exclude'               => true,
    'inputType'             => 'checkbox',
    'foreignKey'            => \Isotope\Model\Payment::getTable().'.name',
    'eval'                  => array('multiple'=>true, 'tl_class'=>'clr w50 w50h'),
    'sql'                   => 'blob NULL',
);

$GLOBALS['TL_DCA']['tl_user']['fields']['iso_payment_modulep'] = array
(
    'label'                 => &$GLOBALS['TL_LANG']['tl_user']['iso_payment_modulep'],
    'exclude'               => true,
    'inputType'             => 'checkbox',
    'options'               => array('create', 'delete'),
    'reference'             => &$GLOBALS['TL_LANG']['MSC'],
    'eval'                  => array('multiple'=>true, 'tl_class'=>'w50 w50h'),
    'sql'                   => 'blob NULL',
);

$GLOBALS['TL_DCA']['tl_user']['fields']['iso_shipping_modules'] = array
(
    'label'                 => &$GLOBALS['TL_LANG']['tl_user']['iso_shipping_modules'],
    'exclude'               => true,
    'inputType'             => 'checkbox',
    'foreignKey'            => \Isotope\Model\Shipping::getTable().'.name',
    'eval'                  => array('multiple'=>true, 'tl_class'=>'clr w50 w50h'),
    'sql'                   => 'blob NULL',
);

$GLOBALS['TL_DCA']['tl_user']['fields']['iso_shipping_modulep'] = array
(
    'label'                 => &$GLOBALS['TL_LANG']['tl_user']['iso_shipping_modulep'],
    'exclude'               => true,
    'inputType'             => 'checkbox',
    'options'               => array('create', 'delete'),
    'reference'             => &$GLOBALS['TL_LANG']['MSC'],
    'eval'                  => array('multiple'=>true, 'tl_class'=>'w50 w50h'),
    'sql'                   => 'blob NULL',
);

$GLOBALS['TL_DCA']['tl_user']['fields']['iso_tax_classes'] = array
(
    'label'                 => &$GLOBALS['TL_LANG']['tl_user']['iso_tax_classes'],
    'exclude'               => true,
    'inputType'             => 'checkbox',
    'foreignKey'            => \Isotope\Model\TaxClass::getTable().'.name',
    'eval'                  => array('multiple'=>true, 'tl_class'=>'clr w50 w50h'),
    'sql'                   => 'blob NULL',
);

$GLOBALS['TL_DCA']['tl_user']['fields']['iso_tax_classp'] = array
(
    'label'                 => &$GLOBALS['TL_LANG']['tl_user']['iso_tax_classp'],
    'exclude'               => true,
    'inputType'             => 'checkbox',
    'options'               => array('create', 'delete'),
    'reference'             => &$GLOBALS['TL_LANG']['MSC'],
    'eval'                  => array('multiple'=>true, 'tl_class'=>'w50 w50h'),
    'sql'                   => 'blob NULL',
);

$GLOBALS['TL_DCA']['tl_user']['fields']['iso_tax_rates'] = array
(
    'label'                 => &$GLOBALS['TL_LANG']['tl_user']['iso_tax_rates'],
    'exclude'               => true,
    'inputType'             => 'checkbox',
    'foreignKey'            => \Isotope\Model\TaxRate::getTable().'.name',
    'eval'                  => array('multiple'=>true, 'tl_class'=>'clr w50 w50h'),
    'sql'                   => 'blob NULL',
);

$GLOBALS['TL_DCA']['tl_user']['fields']['iso_tax_ratep'] = array
(
    'label'                 => &$GLOBALS['TL_LANG']['tl_user']['iso_tax_ratep'],
    'exclude'               => true,
    'inputType'             => 'checkbox',
    'options'               => array('create', 'delete'),
    'reference'             => &$GLOBALS['TL_LANG']['MSC'],
    'eval'                  => array('multiple'=>true, 'tl_class'=>'w50 w50h'),
    'sql'                   => 'blob NULL',
);

$GLOBALS['TL_DCA']['tl_user']['fields']['iso_configs'] = array
(
    'label'                 => &$GLOBALS['TL_LANG']['tl_user']['iso_configs'],
    'exclude'               => true,
    'inputType'             => 'checkbox',
    'foreignKey'            => \Isotope\Model\Config::getTable().'.name',
    'eval'                  => array('multiple'=>true, 'tl_class'=>'clr w50 w50h'),
    'sql'                   => 'blob NULL',
);

$GLOBALS['TL_DCA']['tl_user']['fields']['iso_configp'] = array
(
    'label'                 => &$GLOBALS['TL_LANG']['tl_user']['iso_configp'],
    'exclude'               => true,
    'inputType'             => 'checkbox',
    'options'               => array('create', 'delete'),
    'reference'             => &$GLOBALS['TL_LANG']['MSC'],
    'eval'                  => array('multiple'=>true, 'tl_class'=>'w50 w50h'),
    'sql'                   => 'blob NULL',
);

$GLOBALS['TL_DCA']['tl_user']['fields']['iso_groups'] = array
(
    'label'                 => &$GLOBALS['TL_LANG']['tl_user']['iso_groups'],
    'exclude'               => true,
    'inputType'             => 'checkbox',
    'options_callback'      => array('\Isotope\Backend\User\Callback', 'getGroups'),
    'eval'                  => array('multiple'=>true, 'tl_class'=>'clr w50 w50h'),
    'sql'                   => 'blob NULL',
);

$GLOBALS['TL_DCA']['tl_user']['fields']['iso_groupp'] = array
(
    'label'                 => &$GLOBALS['TL_LANG']['tl_user']['iso_groupp'],
    'exclude'               => true,
    'inputType'             => 'checkbox',
    'options'               => array('create', 'delete', 'rootPaste'),
    'reference'             => &$GLOBALS['TL_LANG']['MSC'],
    'eval'                  => array('multiple'=>true, 'tl_class'=>'w50 w50h'),
    'sql'                   => 'blob NULL',
);

$GLOBALS['TL_DCA']['tl_user']['fields']['iso_member_groups'] = array
(
    'label'                 => &$GLOBALS['TL_LANG']['tl_user']['iso_member_groups'],
    'exclude'               => true,
    'inputType'             => 'checkbox',
    'options_callback'      => array('\Isotope\Backend\User\Callback', 'getMemberGroups'),
    'eval'                  => array('multiple'=>true, 'isAssociative' => true, 'tl_class'=>'clr'),
    'sql'                   => 'blob NULL',
);
