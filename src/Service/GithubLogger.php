<?php

namespace App\Service;

use App\Model\UserManager;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class GithubLogger
{
    private string $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }
    public function getUserData(): array
    {
        $client = HttpClient::create();
        $formFields = [
            'scope' => 'user:email',
            'client_id' => GITHUB_CLIENT,
            'client_secret' => GITHUB_SECRET,
            'code' => $this->code,
            'redirect_uri' => REDIRECT_URI,
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
                'headers' => ['Authorization' => $params['token_type'] . ' ' . $params['access_token']],
            ]
        );
        $userData = json_decode($response->getContent());
        return [
            'pseudo' => $userData->login,
            'avatar' => $userData->avatar_url,
        ];
    }

    public function getAndPersist($userData): array
    {
        $userManager = new UserManager();
        if (!$userManager->selectOneByPseudo($userData['pseudo'])) {
            $userId = $userManager->createUser($userData);
            $user = $userManager->selectOneById($userId);
        } else {
            $user = $userManager->selectOneByPseudo($userData['pseudo']);
        }
        return $user;
    }
}
