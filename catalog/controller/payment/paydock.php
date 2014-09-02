<?php

class ControllerPaymentPaydock extends Controller
{
    protected function index ()
    {
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['button_back'] = $this->language->get('button_back');

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $gateway_url = 'https://paydock.io/api/orders';

        $reference = $this->session->data['order_id'];
        $amount = number_format($order_info['total'], 2, '.', '');

        $callbackUrl = $this->url->link('payment/paydock/callback', '', 'SSL');
        $successUrl = $this->url->link('checkout/success');		

        $order_items = array();
        foreach ($this->cart->getProducts() as $item) {
			$order_items[] = array('name'=>$item['name'], 'quantity'=>(int)$item['quantity'], 'price'=>number_format($item['price'], 2, '.', ''));
        }
		
		if ($this->cart->hasShipping())
			$order_items[] = array('name'=>'Shipping', 'quantity'=>1, 'price'=>number_format($this->session->data['shipping_method']['cost'], 2, '.', ''));
        
		$request = array('currency'=>$order_info['currency_code'], 
							'price'=>$amount, 
							'order_items'=>$order_items,
							'notification_url'=>$callbackUrl,
							'return_url'=>$successUrl,
							'reference'=>$reference
							);


        $request = json_encode($request);

        $result = $this->sendRequest($gateway_url, $request);
        //echo $result;exit;
        $result = json_decode($result);

        if (isset($result->uuid)) {
            $uuid = $result->uuid;
            $this->data['redirect_url'] = 'https://paydock.io/invoice/'.$uuid;

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/paydock.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/paydock.tpl';
            } else {
                $this->template = 'default/template/payment/paydock.tpl';
            }

            $this->render();
        } else {
            $message = 'Payment option currently not available, please contact support';
            header('HTTP/1.1 400 Payment Request Error');
            exit($message);
        }
    }

    public function callback ()
    {
        $secret_key = $this->config->get('paydock_secret_key');

        $status = isset($_POST['status'])?$_POST['status']:'';
        if($status == 'confirmed'){
            $order_id = (int)$_POST['reference'];
            $paid_amount = $_POST['price'];
    		$ipn_digest = $_POST['ipn_digest'];
			
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($order_id);
            if (!$order_info) return;

            $order_amount = number_format($order_info['total'], 2, '.', '');

            $query = $_POST['uuid'].$_POST['status'].$_POST['price'];
            $hash = hash_hmac("sha256", $query, $secret_key);

            if ($ipn_digest == $hash) {
                //success transaction
                $this->load->model('checkout/order');
                $this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'));
                $this->model_checkout_order->update($order_id, $this->config->get('paydock_order_status_id'), 'Invoice ID: ' . $_POST['uuid'], false);
				//mail('vnphpexpert@gmail.com', 'Paydock Success: ' . $paid_amount . ': ' . $_POST['uuid']);
            } else {
                //failed transaction
				//mail('vnphpexpert@gmail.com', 'Paydock Error 1: ' . $paid_amount . ': ' . $_POST['uuid']);
            }
        } else {
			//mail('vnphpexpert@gmail.com', 'Paydock Error 2: ' . $status . ': ' . $_POST['uuid']);
		}
        exit;
    }

    public function sendRequest ($gateway_url, $request)
    {
		$api_key = $this->config->get('paydock_api_key');
        $secret_key = $this->config->get('paydock_secret_key');
		
		$headers = array(
						'Content-Type:application/json',
						'Authorization: Basic '. base64_encode("$api_key:$secret_key"),
						'X-PayDock-Plugin: opencart'
						);
		
        $CR = curl_init();
        curl_setopt($CR, CURLOPT_URL, $gateway_url);
		curl_setopt($CR, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($CR, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($CR, CURLOPT_TIMEOUT, 30);
		curl_setopt($CR, CURLOPT_POST, true);
        curl_setopt($CR, CURLOPT_FAILONERROR, true);
        curl_setopt($CR, CURLOPT_POSTFIELDS, $request);
        curl_setopt($CR, CURLOPT_RETURNTRANSFER, true);

        //actual curl execution perfom
        $result = curl_exec($CR);
        $error = curl_error($CR);

        // on error - die with error message
        if (!empty($error)) {
            die($error);
        }

        curl_close($CR);

        return $result;
    }
}
?>