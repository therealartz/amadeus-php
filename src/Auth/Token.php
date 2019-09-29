<?php declare(strict_types=1);

namespace Amadeus\Auth;

class Token
{
    // Renew the token 10 seconds earlier than required
    private const REFRESH_BUFFER = 10;

    private $username;

    private $applicationName;

    private $clientId;

    private $tokenType;

    private $accessToken;

    private $expiresAt;

    private $state;

    private $scope;

    public function __construct(
        string $username,
        string $applicationName,
        string $clientId,
        string $tokenType,
        string $accessToken,
        int $expiresAt,
        string $state,
        string $scope
    ) {
        $this->username = $username;
        $this->applicationName = $applicationName;
        $this->clientId = $clientId;
        $this->tokenType = $tokenType;
        $this->accessToken = $accessToken;
        $this->expiresAt = $expiresAt;
        $this->state = $state;
        $this->scope = $scope;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getApplicationName(): string
    {
        return $this->applicationName;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getExpiresAt(): int
    {
        return $this->expiresAt;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function needsRefresh(): bool
    {
        return (time() - self::REFRESH_BUFFER) >= $this->getExpiresAt();
    }

    public function getHeaderString(): string
    {
        return $this->getTokenType() . ' ' . $this->getAccessToken();
    }
}
