<?php

/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Service\GithubLogger;
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
            "client_id" => GITHUB_CLIENT,
            "redirect_uri" => REDIRECT_URI,
            "access_type" => "online",
            "response_type" => "code",
        ];
        $url = 'https://github.com/login/oauth/authorize?' . http_build_query($stringParam);

        return $this->twig->render('Home/index.html.twig', [
            'url' => $url
        ]);
    }

    public function connect()
    {
        $logger = new GithubLogger($_GET['code']);
        $userData = $logger->getUserData();
        $user = $logger->getAndPersist($userData);
        $_SESSION['user'] = $user;
        var_dump($_SESSION);
        return $this->twig->render('Home/connect.html.twig', [
            'user' => $user,
        ]);
    }
}
