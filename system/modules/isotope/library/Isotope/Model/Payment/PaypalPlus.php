<?php

/*
 * Isotope eCommerce for Contao Open Source CMS
 *
 * Copyright (C) 2009 - 2019 terminal42 gmbh & Isotope eCommerce Workgroup
 *
 * @link       https://isotopeecommerce.org
 * @license    https://opensource.org/licenses/lgpl-3.0.html
 */

namespace Isotope\Model\Payment;

use GuzzleHttp\Psr7\Response;
use Isotope\Interfaces\IsotopeProductCollection;
use Isotope\Interfaces\IsotopePurchasableCollection;
use Isotope\Model\ProductCollection\Order;
use Isotope\Module\Checkout;
use Isotope\Template;

class PaypalPlus extends PaypalApi
{
    public function checkoutForm(IsotopeProductCollection $objOrder, \Module $objModule)
    {
        if (!$objOrder instanceof IsotopePurchasableCollection) {
            \System::log('Product collection ID "' . $objOrder->getId() . '" is not purchasable', __METHOD__, TL_ERROR);
            Checkout::redirectToStep(Checkout::STEP_COMPLETE, $objOrder);
        }

        $request = $this->createPayment($objOrder);

        if ($request instanceof Response) {
            $responseCode  = (int) $request->getStatusCode();
            $responseError = $request->getReasonPhrase();
            $responseData  = $request->getBody()->getContents();
        } else {
            $responseCode = (int) $request->code;
            $responseError = $request->error;
            $responseData = $request->response;
        }

        $this->debugLog($responseData);

        if (201 === $responseCode) {
            $paypalData = json_decode($responseData, true);
            $this->storePayment($objOrder, $paypalData);
            $this->storeHistory($objOrder, $paypalData);

            $this->patchPayment($objOrder, $paypalData['id']);

            foreach ($paypalData['links'] as $link) {
                if ('approval_url' === $link['rel']) {
                    $template = new Template('iso_payment_paypal_plus');
                    $template->setData($this->arrData);
                    $template->approval_url = $link['href'];
                    $template->mode = $this->debug ? 'sandbox' : 'live';
                    $template->country = strtoupper($objOrder->getBillingAddress()->country);

                    return $template->parse();
                }
            }
        }

        \System::log('PayPayl payment failed. See paypal.log for more information.', __METHOD__, TL_ERROR);

        $this->debugLog(
            sprintf(
                "PayPal API Error! (HTTP %s %s)\n\nResponse:\n%s",
                $responseCode,
                $responseError,
                $responseData
            )
        );

        Checkout::redirectToStep(Checkout::STEP_FAILED);
        exit;
    }

    /**
     * {@inheritdoc}
     */
    public function backendInterface($orderId)
    {
        if (($objOrder = Order::findByPk($orderId)) === null) {
            return parent::backendInterface($orderId);
        }

        $arrPayment = deserialize($objOrder->payment_data, true);

        if (!\is_array($arrPayment['PAYPAL_HISTORY']) || empty($arrPayment['PAYPAL_HISTORY'])) {
            return parent::backendInterface($orderId);
        }

        $strBuffer = '
<div id="tl_buttons">
<a href="' . ampersand(str_replace('&key=payment', '', \Environment::get('request'))) . '" class="header_back" title="' . specialchars($GLOBALS['TL_LANG']['MSC']['backBT']) . '">' . $GLOBALS['TL_LANG']['MSC']['backBT'] . '</a>
</div>';

        foreach ($arrPayment['PAYPAL_HISTORY'] as $response) {
            if ($response['intent'] === 'sale'
                && $response['state'] === 'approved'
                && isset($response['transactions'][0]['related_resources'][0]['sale']['id'])
            ) {
                $saleId = $response['transactions'][0]['related_resources'][0]['sale']['id'];

                $strBuffer .= '
<div class="maintenance_inactive">
<h2 class="sub_headline">' . $this->name . ' (' . $GLOBALS['TL_LANG']['MODEL']['tl_iso_payment'][$this->type][0] . ')' . '</h2>
<div class="tl_tbox">
<p><strong>' . sprintf($GLOBALS['TL_LANG']['MSC']['paypalTransaction'], $saleId) . '</strong></p>
<p>' . $GLOBALS['TL_LANG']['MSC']['paypalTransactionOnline'] .'</p>
<a class="tl_submit" href="https://www.paypal.com/activity/payment/' . $saleId . '" target="_blank">' . $GLOBALS['TL_LANG']['MSC']['paypalTransactionButton'] . '</a>
</div>
</div>';

                break;
            }
        }

        foreach (array_reverse($arrPayment['PAYPAL_HISTORY']) as $transaction) {
            if (isset($transaction['create_time'])) {
                $dateCreated = \Date::parse(
                    $GLOBALS['TL_CONFIG']['datimFormat'],
                    strtotime($transaction['create_time'])
                );
            } else {
                $dateCreated = '<i>UNKNOWN</i>';
            }

            $strBuffer .= '
<div class="maintenance_inactive">
<h2 class="sub_headline">' . sprintf($GLOBALS['TL_LANG']['MSC']['paypalTransactionDetails'], $dateCreated) . '</h2>
<table class="tl_show">
  <tbody>
';

            $render = function($k, $v, &$i) use (&$strBuffer) {
                $strBuffer .= '
  <tr>
    <td' . ($i % 2 ? '' : ' class="tl_bg"') . ' style="width:auto"><span class="tl_label">' . $k . ': </span></td>
    <td' . ($i % 2 ? '' : ' class="tl_bg"') . '>' . $v . '</td>
  </tr>';

                ++$i;
            };

            $loop = function($data, $loop, $i=0) use ($render, &$strBuffer) {
                foreach ($data as $k => $v) {
                    if (\in_array($k, ['potential_payer_info', 'links', 'create_time'], true)) {
                        continue;
                    }

                    if (\is_array($v)) {
                        $strBuffer .= '
  <tr>
    <td' . ($i % 2 ? '' : ' class="tl_bg"') . ' style="width:auto"><span class="tl_label">' . $k . ': </span></td>
    <td' . ($i % 2 ? '' : ' class="tl_bg"') . '>
      <table class="tl_show" style="border:1px solid #d0d0d2; background:#fff"><tbody>';

                        $i++;
                        $loop($v, $loop, (int) $i % 2);

                        $strBuffer .= '</td></tbody></table></tr>';

                        continue;
                    }

                    $render($k, $v, $i);
                }
            };

            $loop($transaction, $loop);

            $strBuffer .= '
</tbody></table>
</div>';
        }

        return $strBuffer;
    }

    /**
     * @inheritdoc
     */
    public function processPayment(IsotopeProductCollection $objOrder, \Module $objModule)
    {
        if (!$objOrder instanceof IsotopePurchasableCollection) {
            \System::log('Product collection ID "' . $objOrder->getId() . '" is not purchasable', __METHOD__, TL_ERROR);
            return false;
        }

        $paypalData = $this->retrievePayment($objOrder);

        if (0 === \count($paypalData)
            || \Input::get('paymentId') !== $paypalData['id']
            || 'created' !== $paypalData['state']
        ) {
            return false;
        }

        /*$request = $this->patchPayment($objOrder, $paypalData['id']);

        if (200 !== $request->code) {
            return false;
        }*/

        $request = $this->executePayment($paypalData['id'], \Input::get('PayerID'));

        if ($request instanceof Response) {
            $responseCode = (int) $request->getStatusCode();
            $responseData = $request->getBody()->getContents();
        } else {
            $responseCode = (int) $request->code;
            $responseData = $request->response;
        }

        $this->debugLog($responseData);

        if (200 !== $responseCode) {
            return false;
        }

        $this->storeHistory($objOrder, json_decode($responseData, true));

        $objOrder->checkout();
        $objOrder->setDatePaid(time());
        $objOrder->updateOrderStatus($this->new_order_status);
        $objOrder->save();

        return true;
    }
}
