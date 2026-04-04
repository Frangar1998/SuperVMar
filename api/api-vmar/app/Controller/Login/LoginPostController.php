<?php

namespace SuperVMar\App\Controller\Login;

use JsonException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use SuperVMar\Authentication\Application\AuthenticationCommand;
use SuperVMar\Authentication\Infrastructure\Symfony\SecurityUser;
use SuperVMar\Shared\Domain\Bus\Command\CommandBus;
use SuperVMar\Shared\Domain\Bus\Query\QueryBus;
use SuperVMar\Shared\Domain\Exception\MandatoryParamsException;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use SuperVMar\Shared\Infrastructure\Symfony\ApiExceptionHttpStatusCodeMapping;
use SuperVMar\User\Application\Search\UserLogin\SearchUserLoginQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class LoginPostController extends ApiController
{
    public function __construct(
        QueryBus $queryBus,
        CommandBus $commandBus,
        ApiExceptionHttpStatusCodeMapping $exceptionHandler,
        private JWTTokenManagerInterface $jwtManager,
    )
    {
        parent::__construct($queryBus, $commandBus, $exceptionHandler);
    }

    /**
     * @throws MandatoryParamsException
     * @throws JsonException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $data = $this->dataFromRequest($request);
        $this->dispatch(
            new AuthenticationCommand(
                $data['username'],
                $data['password'],
                true
            )
        );

        $user = $this->ask(
            new SearchUserLoginQuery(
                $data['username']
            )
        );

        $userData = $user->toArray();

        $securityUser = new SecurityUser(
            $userData['id'],
            $userData['username'],
            '',
            $userData['isAdmin'],
            $userData['job'] ?? null,
        );

        $token = $this->jwtManager->create($securityUser);

        return new JsonResponse(
            data: [
                'token' => $token,
                'id' => $userData['id'],
                'username' => $userData['username'],
                'isAdmin' => $userData['isAdmin'],
                'job' => $userData['job'] ?? null,
            ],
            status: Response::HTTP_ACCEPTED
        );
    }

    protected function mandatoryParams(): array
    {
        return ['username', 'password'];
    }
}