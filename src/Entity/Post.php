<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PostRepository;
use App\Controller\PostCountController;
use App\Controller\PostImageController;
use App\Controller\PostCompletController;
use App\Controller\PostPublishController;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * @Vich\Uploadable()
 */
#[ApiResource(
    normalizationContext:['groups'=>['read:collection']],
    denormalizationContext:['groups'=> ['write:Post']],
    paginationItemsPerPage:3,
    paginationMaximumItemsPerPage:10,
    paginationClientItemsPerPage:true,
    collectionOperations:[
        'get',
        'getAdmin'=>[
            'method'=>'GET',
            'path'=>'/posts/admin',
            'pagination_items_per_page'=>20,
            "pagination_maximum_items_per_page"=>20
        ],
        'post',
        'postComplet'=>[
            'method'=>'POST',
            'path'=>'/posts/new',
            'controller'=>PostCompletController::class,
            'deserialize'=>false,
        ],
        'count'=>[
            'method'=>'GET',
            'path'=>'/posts/count',
            'controller'=>PostCountController::class,
            'read'=>false,
            'filters'=>[],
            'pagination_enabled'=>false,
            'openapi_context'=>[
                'summary' => 'Récupère le nombre total d\'article',
                'parameters' => [
                    [
                        'in' => 'query',
                        'name' => 'online',
                        'schema' => [
                            'type' => 'integer',
                            'maximum' => 1,
                            'minimum' => 0
                        ],
                        'description' => 'Filtre les articles en ligne',
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'OK',
                        'content' => [
                            'application/json'=>[
                                'schema'=>[
                                'type'=>'integer',
                                'example'=>3 
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
    ],
    itemOperations:[
        'put',
        'delete',
        'get'=>[
            'normalization_context' => ['groups'=>['read:collection', "read:item","read:Post"]]
        ],
        'image'=>[
            'method'=>'POST',
            'path'=>'/posts/{id}/image',
            'controller'=>PostImageController::class,
            'deserialize'=>false,
        ],
        'publish'=> [
            'method'=> 'POST',
            'path'=>'/posts/{id}/publish',
            'controller'=>PostPublishController::class,
            'openapi_context'=>[
                'summary'=>'Permet de publier un article',
                'requestBody'=>[
                    'content'=>[
                        'application/json'=>[
                            'schema'=>[]
                        ]
                    ]
                ]
            ]
                        ]
        ]
    ),
    ApiFilter(SearchFilter::class, properties:['id'=>'exact','title'=>'partial'])    ]
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:collection'])]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Groups(['read:collection','write:Post']),
        Length(min: 5, groups:["create:Post"])
    ]
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:collection','write:Post'])]
    private $slug;

    /**
     * @ORM\Column(type="text")
     */
    #[Groups(['read:item','write:Post','read:collection'])]
    private $content;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    #[Groups(['read:item'])]
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="posts", cascade={"persist"})
     */
    #[
        Groups(['read:item','write:Post','read:collection']),
        Valid()    
    ]
    private $category;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
    #[
        Groups(['read:collection']),
        ApiProperty(openapiContext:['type'=>'boolean','description'=>'En ligne ou pas ?'])
    ]
    private $online = false;


    /**
     * @var File|null
     * @Vich\UploadableField(mapping="post_image", fileNameProperty="filePath")
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filePath;

    /**
     * @var string|null
     */
    #[Groups('read:collection')]
    private $fileUrl;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(bool $online): self
    {
        $this->online = $online;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Get the value of file
     *
     * @return  File|null
     */ 
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the value of file
     *
     * @param  File|null  $file
     *
     * @return  self
     */ 
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get the value of fileUrl
     *
     * @return  string|null
     */ 
    public function getFileUrl()
    {
        return $this->fileUrl;
    }

    /**
     * Set the value of fileUrl
     *
     * @param  string|null  $fileUrl
     *
     * @return  self
     */ 
    public function setFileUrl($fileUrl)
    {
        $this->fileUrl = $fileUrl;

        return $this;
    }
}
