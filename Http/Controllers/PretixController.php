<?php

namespace Modules\PretixIntegration\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PretixController extends Controller
{
    public function ajax(Request $request)
    {
        $response = ['status' => 'error', 'msg' => ''];

        switch ($request->input('action')) {
            case 'get_orders':
                $response = $this->handleGetOrders($request);
                break;

            default:
                $response['msg'] = __('Unknown action.');
        }

        return \Response::json($response);
    }

    private function handleGetOrders(Request $request): array
    {
        $email = trim($request->input('email', ''));
        if (!$email) {
            return ['status' => 'error', 'msg' => __('No customer email address available.')];
        }

        $baseUrl   = \Option::get('pretixintegration.base_url', '');
        $apiToken  = \Option::get('pretixintegration.api_token', '');
        $organizer = \Option::get('pretixintegration.organizer', '');

        if (!$baseUrl || !$apiToken || !$organizer) {
            return [
                'status' => 'error',
                'msg'    => __('Pretix Integration is not fully configured. Please visit Settings → Pretix Integration.'),
            ];
        }

        try {
            $orders = $this->fetchOrdersByEmail($baseUrl, $apiToken, $organizer, $email);

            $html = \View::make('pretixintegration::partials/orders', [
                'orders'    => $orders,
                'base_url'  => $baseUrl,
                'organizer' => $organizer,
                'email'     => $email,
            ])->render();

            return ['status' => 'success', 'html' => $html];
        } catch (RequestException $e) {
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;
            \Helper::logException($e, '[Pretix Integration]');

            if ($statusCode === 401 || $statusCode === 403) {
                return ['status' => 'error', 'msg' => __('Pretix API authentication failed. Check your API token in Settings.')];
            }
            if ($statusCode === 404) {
                return ['status' => 'error', 'msg' => __('Pretix organizer not found. Check the organizer slug in Settings.')];
            }

            return ['status' => 'error', 'msg' => __('Could not reach the Pretix API. Please try again later.')];
        } catch (\Exception $e) {
            \Helper::logException($e, '[Pretix Integration]');
            return ['status' => 'error', 'msg' => __('An unexpected error occurred while loading Pretix bookings.')];
        }
    }

    private function fetchOrdersByEmail(string $baseUrl, string $apiToken, string $organizer, string $email): array
    {
        $client = new Client(['timeout' => 15]);

        $url = rtrim($baseUrl, '/') . '/api/v1/organizers/' . rawurlencode($organizer) . '/orders/';

        $httpResponse = $client->get($url, [
            'headers' => [
                'Authorization' => 'Token ' . $apiToken,
                'Accept'        => 'application/json',
            ],
            'query' => [
                'email'    => $email,
                'ordering' => '-datetime',
                'expand'   => 'positions.item',
            ],
        ]);

        $data = json_decode($httpResponse->getBody()->getContents(), true);
        return $data['results'] ?? [];
    }
}
