<?php

/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use http\Client;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class HomeController extends AbstractController
{
    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function login()
    {
        $stringParam = [
            "client_id" => 'dc37bed4efbdd799267e',
            "redirect_uri" => 'http://localhost:8000/connect',
            "access_type" => "online",
            "response_type" => "code",
        ];
        $url = 'https://github.com/login/oauth/authorize?' . http_build_query($stringParam);

        return $this->twig->render('Home/index.html.twig', [
            'wilder' => 'Olivier',
            'url' => $url
        ]);
    }

    public function connect()
    {
        $code = $_GET['code'];
        $client = HttpClient::create();
        $formFields = [
            'client_id' => 'dc37bed4efbdd799267e',
            'client_secret' => '84d1a02888fb01f5c6c51ac7d4cf00605d5a427e',
            'code' => $code,
            'redirect_uri' => 'http://localhost:8000/connect',
        ];
        $formData = new FormDataPart($formFields);
        $response = $client->request(
            'POST',
            'https://github.com/login/oauth/access_token',
            [
                'headers' => $formData->getPreparedHeaders()->toArray(),
                'body' => $formData->bodyToIterable(),
            ]
        );
        $params = [];
        parse_str($response->getContent(), $params);
        $response = $client->request(
            'GET',
            'https://api.github.com/user',
            [
                'headers' => ['Authorization' => 'Bearer ' . $params['access_token']],
            ]
        );
        $userData = json_decode($response->getContent());
        $user = [
            'pseudo' => $userData->login,
            'avatar' => $userData->avatar_url,
        ];
        return $this->twig->render('Home/connect.html.twig', [
            'user' => $user,
        ]);
    }
}
