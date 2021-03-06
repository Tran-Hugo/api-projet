<?php

namespace App\Entity;

use App\Controller\MeController;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\Controller\RegistrationController;
use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
#[ApiResource(
    collectionOperations:[
        'me'=>[
            'pagination_enabled'=>false,
            'path'=>'/me',
            'method'=>'get',
            'controller'=> MeController::class,
            'read'=>false,
            'security'=>'is_granted("ROLE_USER")',
            'openapi_context' => [
                'security' => [['bearerAuth'=>[]]]
            ]
        ],
        'post'=>[
            'controller'=>RegistrationController::class
        ]
    ],
    itemOperations:[
        'get'=>[
            'controller'=> NotFoundAction::class,
            'openapi_context'=> ['summary'=>'hidden'],
            'read'=>false,
            'output'=>false
        ]
        ],
    denormalizationContext:['groups'=>'write:User'],
    normalizationContext:['groups'=>'read:User']
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, JWTUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:User'])]
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    #[Groups(['read:User','write:User'])]
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    #[Groups(['read:User'])]
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    #[Groups(['write:User'])]
    private $password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public static function createFromPayload($id, array $payload){
       
        return (new User)->setId($id)->setEmail($payload['email'] ?? '')->setRoles($payload["roles"]);
    }
}
